"use client";
import PageLayout from "./PageLayout";

export default function PrivacyPolicy() {
  return (
    <PageLayout>
      <section className="container py-5 legal-page no-underline" style={{ minHeight: "60vh" }}>
        <div className="row justify-content-center">
          <div className="col-lg-10">
            <div
              className="p-4 p-md-5 rounded bg-white"
              style={{ boxShadow: "0 8px 20px rgba(0, 51, 153, 0.08)" }}
            >
              <h2 className="mb-3 subN">Privacy Policy</h2>
              <p>
                Ente Keralam collects and processes personal data to provide services and improve user experience.
                This includes account details (name, email, mobile), activity data, and technical information.
              </p>

              <h4 className="subN">Data We Collect</h4>
              <ul>
                <li>Account information: name, email, mobile number</li>
                <li>Activity data: participation in quizzes, polls, pledges, tasks</li>
                <li>Technical data: device, browser, IP, and logs</li>
              </ul>

              <h4 className="subN">How We Use Data</h4>
              <ul>
                <li>To create and manage your account</li>
                <li>To deliver platform features and personalized content</li>
                <li>To communicate important updates, OTPs, and notifications</li>
                <li>To ensure security and prevent fraud</li>
              </ul>

              <h4 className="subN">Sharing</h4>
              <p>
                We do not sell personal data. Limited sharing occurs with trusted service providers (e.g., email/SMS delivery)
                under appropriate safeguards.
              </p>

              <h4 className="subN">Retention and Deletion</h4>
              <p>
                Data is retained for as long as your account is active or required by law. You may request deletion of
                your data at any time via the Data Deletion page or in-app account settings.
              </p>

              {/* <h4 className="subN">Contact</h4>
              <p>
                For questions, reach us at{" "}
                <a href="mailto:support@entekeralam.in">support@entekeralam.in</a>.
              </p> */}
            </div>
          </div>
        </div>
      </section>
    </PageLayout>
  );
}
