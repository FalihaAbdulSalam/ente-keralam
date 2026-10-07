import re
import sqlite3
import os
import shutil

SQL_PATH = "/Users/falihaabdulsalam/Downloads/entekeralamdb.sql"
DB_PATH = "/Users/falihaabdulsalam/Downloads/ente-keralam-main/database/database.sqlite"
PUBLIC_DIR = "/Users/falihaabdulsalam/Downloads/ente-keralam-main/public"
STORAGE_APP_PUBLIC = "/Users/falihaabdulsalam/Downloads/ente-keralam-main/storage/app/public"

def clean_field_value(v):
    if v.upper() == 'NULL':
        return None
    # Strip enclosing quotes if present
    if (v.startswith("'") and v.endswith("'")) or (v.startswith('"') and v.endswith('"')):
        return v[1:-1]
    return v

def parse_mysql_values_block(val_str):
    rows = []
    in_str = False
    escape = False
    depth = 0
    start = -1
    for i, ch in enumerate(val_str):
        if escape:
            escape = False
            continue
        if ch == '\\':
            escape = True
            continue
        if ch == "'":
            in_str = not in_str
            continue
        if not in_str:
            if ch == '(':
                depth += 1
                if depth == 1:
                    start = i + 1
            elif ch == ')':
                depth -= 1
                if depth == 0 and start != -1:
                    rows.append(val_str[start:i])
                    start = -1
    return rows

def parse_fields_from_row(row_str):
    fields = []
    in_str = False
    escape = False
    cur = []
    for ch in row_str:
        if escape:
            if ch == 'n':
                cur.append('\n')
            elif ch == 'r':
                cur.append('\r')
            elif ch == 't':
                cur.append('\t')
            else:
                cur.append(ch)
            escape = False
            continue
        if ch == '\\':
            escape = True
            continue
        if ch == "'":
            in_str = not in_str
            continue
        if not in_str and ch == ',':
            val = ''.join(cur).strip()
            fields.append(clean_field_value(val))
            cur = []
            continue
        cur.append(ch)
    if cur:
        val = ''.join(cur).strip()
        fields.append(clean_field_value(val))
    return fields

def main():
    if not os.path.exists(SQL_PATH):
        print(f"Error: {SQL_PATH} does not exist")
        return

    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("PRAGMA foreign_keys = OFF;")

    with open(SQL_PATH, 'r', encoding='utf-8') as f:
        sql_content = f.read()

    # 1. Primary keys mapping
    known_pks = {
        'tbl_pledge': 'pledge_id',
        'quizzes': 'id',
        'polls': 'id',
        'news': 'id',
        'tasks': 'id',
        'testimonials': 'id',
        'banners': 'id',
        'banner_categories': 'id',
        'creativethoughts': 'id',
        'faqs': 'id',
        'footers': 'id',
        'footer_categories': 'id',
        'districts': 'id',
        'events': 'id',
        'festivals': 'id',
        'campaigns': 'id',
        'eventtypes': 'id',
        'quiz_sections': 'id',
        'question_banks': 'id',
        'question_options': 'id',
        'quiz_questions': 'id',
        'poll_questions': 'id',
        'poll_options': 'id',
        'sector_details': 'id',
        'counters_in_page': 'id',
        'articles': 'id',
        'article_types': 'id',
        'main_menus': 'id',
        'sub_menus': 'id',
        'badges': 'id',
        'activity_categories': 'id',
        'admin_users': 'id',
        'admin_menus': 'id',
        'users': 'id',
        'user_activities': 'id',
        'user_points': 'id',
        'user_settings': 'id',
        'user_skills_interests': 'id',
        'referral_codes': 'id',
        'campaign_messages': 'id',
        'document_file_submissions': 'id',
        'file_submissions': 'id',
        'sector_section_label': 'id',
        'video_file_submissions': 'id',
        'responses_from_api_quiz': 'id',
    }

    # 2. Target tables to import from entekeralamdb.sql
    target_tables = [
        'activity_categories',
        'admin_menus',
        'admin_users',
        'article_types',
        'articles',
        'badges',
        'banner_categories',
        'banners',
        'campaign_messages',
        'campaigns',
        'counters_in_page',
        'creativethoughts',
        'districts',
        'document_file_submissions',
        'events',
        'eventtypes',
        'faqs',
        'festivals',
        'file_submissions',
        'footer_categories',
        'footers',
        'main_menus',
        'sub_menus',
        'news',
        'poll_options',
        'poll_questions',
        'polls',
        'question_banks',
        'question_options',
        'quiz_questions',
        'quiz_sections',
        'quizzes',
        'referral_codes',
        'responses_from_api_quiz',
        'sector_details',
        'sector_section_label',
        'tasks',
        'tbl_pledge',
        'testimonials',
        'user_activities',
        'user_points',
        'user_settings',
        'user_skills_interests',
        'users',
        'video_file_submissions',
    ]

    # 3. Create or recreate schemas
    for tbl in target_tables:
        pattern = r'CREATE TABLE \`' + tbl + r'\` \((.*?)\) ENGINE=[^;]+;'
        m = re.search(pattern, sql_content, re.DOTALL)
        if not m:
            continue

        raw_cols = m.group(1)
        pk_col = known_pks.get(tbl, 'id')

        cursor.execute(f'DROP TABLE IF EXISTS "{tbl}";')

        col_defs = []
        for line in raw_cols.strip().split('\n'):
            line = line.strip().rstrip(',')
            if not line or line.startswith(('KEY', 'PRIMARY KEY', 'UNIQUE KEY', 'CONSTRAINT')):
                continue
            col_match = re.match(r'^\`([^\`]+)\`\s+([A-Za-z0-9_]+(?:\([^\)]+\))?)', line)
            if not col_match:
                continue
            col_name = col_match.group(1)
            raw_type = col_match.group(2).lower()

            if col_name == pk_col:
                col_def = f'"{col_name}" INTEGER PRIMARY KEY AUTOINCREMENT'
            else:
                if any(k in raw_type for k in ['int', 'tinyint', 'bigint', 'smallint', 'mediumint', 'boolean']):
                    sql_type = 'INTEGER'
                elif any(k in raw_type for k in ['decimal', 'float', 'double']):
                    sql_type = 'REAL'
                else:
                    sql_type = 'TEXT'
                col_def = f'"{col_name}" {sql_type}'
            col_defs.append(col_def)

        create_sql = f'CREATE TABLE "{tbl}" (\n  ' + ',\n  '.join(col_defs) + '\n);'
        cursor.execute(create_sql)

    conn.commit()

    # 4. Extract and execute INSERT statements for all target tables
    for tbl in target_tables:
        pattern = r'INSERT INTO \`' + tbl + r'\` \(([^\)]+)\) VALUES\s*(.*?);\n'
        matches = re.findall(pattern, sql_content, re.DOTALL)
        if not matches:
            continue

        total_inserted = 0
        for cols_str, vals_str in matches:
            if not vals_str.strip().endswith(')'):
                vals_str = vals_str + ')'

            cols = [c.strip().strip('`') for c in cols_str.split(',')]
            col_placeholders = ', '.join([f'"{c}"' for c in cols])
            placeholders = ', '.join(['?'] * len(cols))
            insert_sql = f'INSERT OR REPLACE INTO "{tbl}" ({col_placeholders}) VALUES ({placeholders})'

            row_strings = parse_mysql_values_block(vals_str)
            batch = []
            for r_str in row_strings:
                fields = parse_fields_from_row(r_str)
                if len(fields) != len(cols):
                    continue
                batch.append(fields)

            if batch:
                cursor.executemany(insert_sql, batch)
                total_inserted += len(batch)

        print(f"Imported {total_inserted} rows into {tbl}")
        conn.commit()

    # 5. Insert test user 1 for easy login
    cursor.execute("""
        INSERT OR REPLACE INTO users (
            id, name, email, password, profile_completion_percentage, is_profile_complete, is_active
        ) VALUES (
            1, 'Test User', 'test@example.com', '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 100, 1, 1
        )
    """)
    conn.commit()

    # 6. Normalize dates so content is active for current time (2025 - 2027)
    future_date = '2027-12-31'
    past_date = '2025-01-01'

    cursor.execute(f"UPDATE quizzes SET start_date = '{past_date}', end_date = '{future_date}' WHERE status = 1;")
    cursor.execute(f"UPDATE polls SET start_date = '{past_date}', end_date = '{future_date}' WHERE status = 1;")
    cursor.execute(f"UPDATE tbl_pledge SET pledge_status = 1, pledge_startDate = '{past_date}', pledge_endDate = '{future_date}';")
    cursor.execute(f"UPDATE tasks SET last_date = '{future_date}' WHERE status != 'closed';")
    conn.commit()

    # 7. Ensure pledge assets exist in public/uploads/pledge and storage symlink
    pledge_banners_dir = os.path.join(PUBLIC_DIR, "uploads/pledge/banners")
    pledge_posters_dir = os.path.join(PUBLIC_DIR, "uploads/pledge/posters")
    os.makedirs(pledge_banners_dir, exist_ok=True)
    os.makedirs(pledge_posters_dir, exist_ok=True)

    fp_dir = os.path.join(PUBLIC_DIR, "design/assets/fp")
    fp_pledges = [
        os.path.join(fp_dir, f"Pledge - {i}.jpg") for i in range(1, 6)
    ]

    cursor.execute("SELECT pledge_id, poster, banner FROM tbl_pledge;")
    pledge_rows = cursor.fetchall()
    for pid, poster, banner in pledge_rows:
        fallback_fp = fp_pledges[(pid - 1) % len(fp_pledges)]
        if poster:
            dest_poster = os.path.join(PUBLIC_DIR, poster)
            if not os.path.exists(dest_poster) and os.path.exists(fallback_fp):
                os.makedirs(os.path.dirname(dest_poster), exist_ok=True)
                shutil.copyfile(fallback_fp, dest_poster)
        if banner:
            dest_banner = os.path.join(PUBLIC_DIR, banner)
            if not os.path.exists(dest_banner) and os.path.exists(fallback_fp):
                os.makedirs(os.path.dirname(dest_banner), exist_ok=True)
                shutil.copyfile(fallback_fp, dest_banner)

    # 8. Ensure banners symlink
    public_banners_path = os.path.join(PUBLIC_DIR, "banners")
    uploads_banners_path = os.path.join(PUBLIC_DIR, "uploads/banners")
    if not os.path.exists(public_banners_path) and os.path.exists(uploads_banners_path):
        try:
            os.symlink(uploads_banners_path, public_banners_path)
        except Exception as e:
            print("Symlink banners:", e)

    # 9. Ensure storage uploads symlink
    storage_uploads_path = os.path.join(STORAGE_APP_PUBLIC, "uploads")
    public_uploads_path = os.path.join(PUBLIC_DIR, "uploads")
    if not os.path.exists(storage_uploads_path) and os.path.exists(public_uploads_path):
        try:
            os.symlink(public_uploads_path, storage_uploads_path)
        except Exception as e:
            print("Symlink storage uploads:", e)

    # 10. Ensure article posters and banners exist
    cursor.execute("SELECT poster, banner FROM articles;")
    article_rows = cursor.fetchall()
    poster_dir = os.path.join(PUBLIC_DIR, "articles/poster")
    banner_dir = os.path.join(PUBLIC_DIR, "articles/banner")
    os.makedirs(poster_dir, exist_ok=True)
    os.makedirs(banner_dir, exist_ok=True)

    existing_posters = [f for f in os.listdir(poster_dir) if not f.startswith('.')]
    fallback_poster = os.path.join(poster_dir, existing_posters[0]) if existing_posters else None

    for poster, banner in article_rows:
        if poster:
            dest = os.path.join(PUBLIC_DIR, poster)
            if not os.path.exists(dest):
                os.makedirs(os.path.dirname(dest), exist_ok=True)
                base = os.path.basename(poster)
                parts = base.split('_poster_', 1)
                suffix = parts[1] if len(parts) > 1 else base
                found = False
                for cand in existing_posters:
                    if cand.endswith(suffix):
                        shutil.copyfile(os.path.join(poster_dir, cand), dest)
                        found = True
                        break
                if not found and fallback_poster:
                    shutil.copyfile(fallback_poster, dest)

        if banner:
            dest = os.path.join(PUBLIC_DIR, banner)
            if not os.path.exists(dest):
                os.makedirs(os.path.dirname(dest), exist_ok=True)
                poster_dest = os.path.join(PUBLIC_DIR, poster) if poster else None
                if poster_dest and os.path.exists(poster_dest):
                    shutil.copyfile(poster_dest, dest)
                elif fallback_poster:
                    shutil.copyfile(fallback_poster, dest)

    conn.close()
    print("Successfully finished importing entekeralamdb.sql!")

if __name__ == '__main__':
    main()
