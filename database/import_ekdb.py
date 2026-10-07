import re
import sqlite3
import os

SQL_PATH = "/Users/falihaabdulsalam/Downloads/ekdb.sql"
DB_PATH = "/Users/falihaabdulsalam/Downloads/ente-keralam-main/database/database.sqlite"

def main():
    if not os.path.exists(SQL_PATH):
        print(f"Error: {SQL_PATH} does not exist")
        return

    conn = sqlite3.connect(DB_PATH)
    cursor = conn.cursor()
    cursor.execute("PRAGMA foreign_keys = OFF;")

    with open(SQL_PATH, 'r', encoding='utf-8') as f:
        sql_content = f.read()

    # 1. Primary keys
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
    }

    # 2. Target tables to import
    target_tables = [
        'activity_categories',
        'admin_menus',
        'admin_users',
        'article_types',
        'articles',
        'badges',
        'banner_categories',
        'banners',
        'campaigns',
        'counters_in_page',
        'creativethoughts',
        'districts',
        'events',
        'eventtypes',
        'faqs',
        'festivals',
        'footer_categories',
        'footers',
        'main_menus',
        'sub_menus',
        'news',
        'polls',
        'poll_questions',
        'poll_options',
        'question_banks',
        'question_options',
        'quizzes',
        'quiz_questions',
        'quiz_sections',
        'sector_details',
        'sector_section_label',
        'tasks',
        'tbl_pledge',
        'testimonials',
    ]

    # 3. Create schemas
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

    # 4. Parsers
    def parse_mysql_values_block(val_str):
        rows = []
        in_str = False
        escape = False
        quote_char = None
        depth = 0
        start = -1
        for i, ch in enumerate(val_str):
            if escape:
                escape = False
                continue
            if ch == '\\':
                escape = True
                continue
            if ch in ("'", '"'):
                if not in_str:
                    in_str = True
                    quote_char = ch
                elif quote_char == ch:
                    in_str = False
                    quote_char = None
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
            if ch in ("'", '"'):
                if not in_str:
                    in_str = True
                else:
                    in_str = False
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

    def clean_field_value(v):
        if v.upper() == 'NULL':
            return None
        return v

    # 5. Extract and execute INSERT statements
    for tbl in target_tables:
        pattern = r'INSERT INTO \`' + tbl + r'\` \(([^\)]+)\) VALUES\s*(.*?)\);\n'
        matches = re.findall(pattern, sql_content, re.DOTALL)
        if not matches:
            # Fallback for statements ending with ;\n
            pattern_fallback = r'INSERT INTO \`' + tbl + r'\` \(([^\)]+)\) VALUES\s*(.*?);\n'
            matches = re.findall(pattern_fallback, sql_content, re.DOTALL)
            if not matches:
                continue

        total_inserted = 0
        for cols_str, vals_str in matches:
            # Append final closing parenthesis if stripped by regex
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

    # 6. Normalize dates so that quizzes, polls, pledges, and tasks are all active in current local time
    future_date = '2027-12-31'
    past_date = '2025-01-01'

    cursor.execute(f"UPDATE quizzes SET start_date = '{past_date}', end_date = '{future_date}' WHERE status = 1;")
    cursor.execute(f"UPDATE polls SET start_date = '{past_date}', end_date = '{future_date}' WHERE status = 1;")
    cursor.execute(f"UPDATE tbl_pledge SET pledge_startDate = '{past_date}', pledge_endDate = '{future_date}' WHERE pledge_status = 1;")
    cursor.execute(f"UPDATE tasks SET last_date = '{future_date}' WHERE status != 'closed';")
    conn.commit()

    # 7. Additional pledges for complete coverage
    additional_pledges = [
        (
            2,
            'ലഹരി വിരുദ്ധ പ്രതിജ്ഞ (Anti-Drug Pledge)',
            'ലഹരി മുക്ത നവകേരളം സൃഷ്ടിക്കാൻ നമുക്ക് കൈകോർക്കാം. ആരോഗ്യകരമായ ഒരു സമൂഹത്തിനായി ലഹരിക്കെതിരെ പോരാടാം.',
            '<ol><li>ഞാൻ ഒരു തരത്തിലുള്ള ലഹരിപദാർത്ഥങ്ങളും ഉപയോഗിക്കില്ലെന്ന് ഉറപ്പുനൽകുന്നു.</li><li>ലഹരിക്കെതിരെയുള്ള ബോധവൽക്കരണ പ്രവർത്തനങ്ങളിൽ സജീവമായി പങ്കെടുക്കും.</li><li>ലഹരിയുടെ അപകടങ്ങളെക്കുറിച്ച് സുഹൃത്തുക്കളെയും കുടുംബാംഗങ്ങളെയും ബോധവാന്മാരാക്കും.</li><li>ആരോഗ്യകരവും സുരക്ഷിതവുമായ ഒരു ജീവിതശൈലി നയിക്കും.</li></ol>',
            past_date,
            future_date,
            'design/assets/fp/Pledge - 2.jpg',
            'design/assets/fp/Pledge - 2.jpg',
            1,
            50
        ),
        (
            3,
            'ശുചിത്വ കേരളം പ്രതിജ്ഞ (Clean Kerala Pledge)',
            'മാലിന്യമുക്ത കേരളം നമ്മുടെ ലക്ഷ്യം. ഉറവിട മാലിന്യ സംസ്കരണവും പ്ലാസ്റ്റിക് നിരോധനവും ഉറപ്പാക്കാം.',
            '<ol><li>ഞാൻ എന്റെ വീടും പരിസരവും എപ്പോഴും വൃത്തിയായി സൂക്ഷിക്കും.</li><li>മാലിന്യങ്ങൾ പൊതുസ്ഥലങ്ങളിലോ ജലാശയങ്ങളിലോ വലിച്ചെറിയില്ല.</li><li>ജൈവ-അജൈവ മാലിന്യങ്ങൾ തരംതിരിച്ച് ഹരിതകർമസേനയ്ക്ക് കൈമാറും.</li><li>ഒറ്റത്തവണ ഉപയോഗിക്കുന്ന പ്ലാസ്റ്റിക് പൂർണ്ണമായും ഒഴിവാക്കും.</li></ol>',
            past_date,
            future_date,
            'design/assets/fp/Pledge - 3.jpg',
            'design/assets/fp/Pledge - 3.jpg',
            1,
            50
        ),
        (
            4,
            'സ്ത്രീ സുരക്ഷാ പ്രതിജ്ഞ (Women Safety & Equality Pledge)',
            'ലിംഗസമത്വവും സ്ത്രീ സുരക്ഷയും ഉറപ്പുവരുത്തി തുല്യതയുള്ള ഒരു കേരളം കെട്ടിപ്പടുക്കാം.',
            '<ol><li>സ്ത്രീകളെ ബഹുമാനിക്കുകയും തുല്യഅവകാശങ്ങൾ ഉറപ്പുനൽകുകയും ചെയ്യും.</li><li>സ്ത്രീകൾക്കെതിരെയുള്ള അതിക്രമങ്ങൾക്കെതിരെ ശബ്ദമുയർത്തും.</li><li>സുരക്ഷിതമായ തൊഴിലിടങ്ങളും പൊതുഇടങ്ങളും സൃഷ്ടിക്കാൻ സഹായിക്കും.</li></ol>',
            past_date,
            future_date,
            'design/assets/fp/Pledge - 4.jpg',
            'design/assets/fp/Pledge - 4.jpg',
            1,
            50
        ),
        (
            5,
            'ഡിജിറ്റൽ സാക്ഷരതാ പ്രതിജ്ഞ (Digital Kerala Pledge)',
            'ഡിജിറ്റൽ സേവനങ്ങൾ എല്ലാവരിലേക്കും എത്തിക്കാൻ ഡിജിറ്റൽ സാക്ഷരത വളർത്താം.',
            '<ol><li>സർക്കാർ ഇ-സേവനങ്ങൾ പരമാവധി ഉപയോഗപ്പെടുത്തും.</li><li>മുതിർന്ന പൗരന്മാർക്ക് സ്മാർട്ട്ഫോൺ, ഇന്റർനെറ്റ് ഉപയോഗം പഠിപ്പിക്കും.</li><li>സൈബർ സുരക്ഷാ മാനദണ്ഡങ്ങൾ പാലിച്ച് പ്രവർത്തിക്കും.</li></ol>',
            past_date,
            future_date,
            'design/assets/fp/Pledge - 5.jpg',
            'design/assets/fp/Pledge - 5.jpg',
            1,
            50
        )
    ]

    for p in additional_pledges:
        cursor.execute('''
            INSERT OR REPLACE INTO tbl_pledge 
            (pledge_id, pledge_title, pledge_description, pledge_content, pledge_startDate, pledge_endDate, poster, banner, pledge_status, pledge_score, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
        ''', p)
    conn.commit()

    # 8. Additional sectors in sector_details to match counters_in_page (sectors 2, 3, 4, 5)
    additional_sectors = [
        (
            2,
            'Health',
            'ആരോഗ്യം',
            '<p>Kerala\'s exemplary achievements in public healthcare, longevity, and welfare schemes.</p>',
            '<p>കേരളത്തിന്റെ ആരോഗ്യ രംഗത്തെ മാതൃകാപരമായ നേട്ടങ്ങളും പദ്ധതികളും.</p>',
            'uploads/icons/1767078274_Health 1.gif',
            1,
            'health',
            'design/assets/fp/111.svg',
            'uploads/banners/1764154027_banners-04.jpg'
        ),
        (
            3,
            'Industry & Commerce',
            'വ്യവസായം',
            '<p>Fast-growing startup ecosystem and Ease of Doing Business in Kerala.</p>',
            '<p>കേരളത്തിലെ സംരംഭകത്വ വികസനവും സ്റ്റാർട്ടപ്പ് മുന്നേറ്റങ്ങളും.</p>',
            'uploads/icons/1767086158_startup_14732139.gif',
            1,
            'industry',
            'design/assets/fp/111.svg',
            'uploads/banners/1764154046_banner-1.jpg'
        ),
        (
            4,
            'Education',
            'വിദ്യാഭ്യാസം',
            '<p>Hi-tech classrooms, 100% digital literacy, and public school infrastructure development.</p>',
            '<p>പൊതുവിദ്യാഭ്യാസ സംരക്ഷണ യജ്ഞവും ഹൈടെക് ക്ലാസ് മുറികളും.</p>',
            'uploads/icons/1767084212_school_19011814.gif',
            1,
            'education',
            'design/assets/fp/111.svg',
            'uploads/banners/1764154003_banner-1.1.jpg'
        ),
        (
            5,
            'Infrastructure',
            'അടിസ്ഥാന സൗകര്യം',
            '<p>Vizhinjam international port, National Highway expansion, and international airports.</p>',
            '<p>വിഴിഞ്ഞം തുറമുഖം, ദേശീയപാതാ വികസനം തുടങ്ങി വൻകിട പശ്ചാത്തല വികസനങ്ങൾ.</p>',
            'uploads/icons/1767086265_port_17904065.gif',
            1,
            'infrastructure',
            'design/assets/fp/111.svg',
            'uploads/banners/1764223260_banner-1.1.jpg'
        ),
    ]

    for s in additional_sectors:
        cursor.execute('''
            INSERT OR REPLACE INTO sector_details
            (id, entitle, maltitle, endescription, maldescription, icon, status, link, rupee_icon, poster, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))
        ''', s)
    conn.commit()

    conn.close()
    print("Database import and population completed successfully!")

if __name__ == '__main__':
    main()
