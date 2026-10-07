"use client";
import PageLayout from "./PageLayout";

export default function DataDeletion() {
  return (
    <PageLayout>
      <section className="container py-5 legal-page no-underline" style={{ minHeight: "60vh" }}>
        <div className="row justify-content-center">
          <div className="col-lg-10">
            <div
              className="p-4 p-md-5 rounded bg-white"
              style={{ boxShadow: "0 8px 20px rgba(0, 51, 153, 0.08)" }}
            >
              <h2 className="mb-3 subN">Data Deletion Instructions</h2>
              <p>If you want to delete your Ente Keralam account and personal data:</p>
              <ol>
                <li>
                  Use the <strong>Deactivate Account</strong> option in Account Settings. This immediately deactivates your account and removes access to your data, points, and activities.
                </li>
                {/* <li>
                  If you cannot access the app, email{" "}
                  <a href="mailto:support@entekeralam.in">support@entekeralam.in</a> with your registered email and mobile number and request deletion.
                </li> */}
                <li>
                  If you logged in via Facebook or Google, include your provider name and profile link/ID in the email.
                </li>
                <li>
                  We process deletion requests within 7 business days unless retention is required by law.
                </li>
              </ol>
              <div
                className="mt-3 p-3 rounded"
                style={{ background: "#f7f7f7", borderLeft: "4px solid #039" }}
              >
                <strong>What happens after deletion?</strong>
                <ul className="mb-0 mt-2">
                  <li>Your account is deactivated immediately.</li>
                  <li>Access to all data, activities, points, and achievements is removed.</li>
                  <li>This action is not easily reversible.</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>
    </PageLayout>
  );
}
