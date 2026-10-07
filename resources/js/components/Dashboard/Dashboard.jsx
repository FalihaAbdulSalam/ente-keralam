import { useState } from "react";
import { FaRegClock } from "react-icons/fa";
import {
  FaStar,
  FaTrophy,
  FaTasks,
  FaEdit,
  FaShareSquare,
  FaMobileAlt,
  FaAward,
  FaVideo,
  FaRegComment,
  FaThumbsUp,
  FaRegEdit,
  FaFileDownload,
} from "react-icons/fa";
import { FiSettings } from "react-icons/fi";
import { GrDocumentUpdate } from "react-icons/gr";
import { MdNoAccounts, MdEmail, MdLocalActivity } from "react-icons/md";
import { RiLockPasswordLine } from "react-icons/ri";

import "./Dashboard.css";

// const Dashboard = () => {
//   return (
//     <>
//       <div className="dashboard-container">
//         {/* Left Section */}
//         <div className="left-section">
//           <div className="card card-das profile-card full-height">
//             <div className="bc-sec-1">
//               <div className="bc-sec-title">
//                 <h2>My Profile</h2>
//               </div>
//             </div>

//             <img src="/img/image.png" alt="Profile" className="profile-img" />
//             <h2 className="user-name">Joseph Kuruvila</h2>
//             <h3 className="user-id">User ID: 12345</h3>
//             <img src="/img/badge.png" alt="Badge" className="badge-img" />

//             {/* Menu Section */}
//             <ul className="menu-list">
//               <li className="menu-item ">
//                 <MdLocalActivity  className="menu-icon" />
//                My Activity
//               </li>
//               <li className="menu-item active">
//                 <FaEdit className="menu-icon" />
//                 Edit Profile
//               </li>
//               <li className="menu-item">
//                 <FaTasks className="menu-icon" />
//                 Account Settings
//               </li>
//               <li className="menu-item">
//                 <FaTrophy className="menu-icon" />
//                 Skills and Interests
//               </li>
//               <li className="menu-item">
//                 <GrDocumentUpdate className="menu-icon" />
//                 Update Profile Picture
//               </li>
//               <li className="menu-item">
//                 {/* <img src="/img/setting.png" alt="settings" className="menu-icon-img" /> */}
//                 <MdEmail className="menu-icon" />
//                 Update Email Id
//               </li>
//               <li className="menu-item">
//                 <FaMobileAlt className="menu-icon" />
//                 Update Mobile Number
//               </li>
//               <li className="menu-item">
//                 <RiLockPasswordLine className="menu-icon" />
//                 Update Password
//               </li>
//               <li className="menu-item">
//                 <MdNoAccounts className="menu-icon" />
//                 Deactivate Account
//               </li>{" "}
//               <li className="menu-item">
//                 <FaShareSquare className="menu-icon" />
//                 Referral Code
//               </li>
//             </ul>
//           </div>
//         </div>

//         <div className="right-section">
//           <div className="stats-cards">
//             <div className="card card-das stat-card">
//               <div className="icon-bg">
//                 {/* <FaStar /> */}
//                 <img src="/img/point.svg" alt="Badge" className="" />
//               </div>
//               <p className="card-1">520</p>
//               <h4>Total Points</h4>
//             </div>

//             <div className="card card-das stat-card">
//               <div className="icon-bg">
//                 {/* <FaTrophy /> */}
//                 <img src="/img/level.svg" alt="Badge" className="" />
//               </div>
//               <p className="card-2">Master</p>
//               <h4>Level</h4>
//             </div>

//             <div className="card card-das stat-card">
//               <div className="icon-bg">
//                 {/* <FaTasks /> */}
//                 <img src="/img/puzzle.svg" alt="Badge" className="" />
//               </div>
//               <p className="card-3">10</p>
//               <h4>Activities</h4>
//             </div>

//             {/* <div className="card card-das stat-card">
//               <div className="icon-bg">
//                 <img src="/img/badge.svg" alt="Badge" className="" />
//               </div>
//               <img src="/img/badge.png" alt="Badge" className="badge-img" />
//               <h4>Badge</h4>
//             </div> */}
//           </div>

//           {/* History Section */}
//           <div className="card card-das history-card">
//             <h3>History</h3>
//             <ul>
//               <li>
//                 <FaRegClock className="history-icon" />
//                 <div className="history-content-row">
//                   <span className="history-date">Oct 6, 2025</span>
//                   <p>Participated in National Quiz</p>
//                 </div>
//               </li>

//               <li>
//                 <FaAward className="history-icon" />
//                 <div className="history-content-row">
//                   <span className="history-date">Oct 06, 2025</span>
//                   <p>
//                     Task is accepted International Year of Cooperatives 2025
//                     Essay Competition
//                   </p>
//                 </div>
//               </li>

//               <li>
//                 <FaVideo className="history-icon" />
//                 <div className="history-content-row">
//                   <span className="history-date">Aug 30, 2025</span>
//                   <p>
//                     Task is accepted Reel Making Contest for Engaging Youth in
//                     Emerging STI for Viksit Bharat
//                   </p>
//                 </div>
//               </li>

//               <li>
//                 <FaRegComment className="history-icon" />
//                 <div className="history-content-row">
//                   <span className="history-date">Aug 14, 2024</span>
//                   <p>
//                     Replied Good info on Post Express your Patriotism through
//                     Stories and Experiences
//                   </p>
//                 </div>
//               </li>

//               <li>
//                 <FaThumbsUp className="history-icon" />
//                 <div className="history-content-row">
//                   <span className="history-date">Aug 14, 2024</span>
//                   <p>
//                     Liked the comment Sir, on the Discussion Express your
//                     Patriotism through Stories and Experiences
//                   </p>
//                 </div>
//               </li>

//               <li>
//                 <FaRegEdit className="history-icon" />
//                 <div className="history-content-row">
//                   <span className="history-date">Aug 09, 2024</span>
//                   <p>
//                     Posted Vande Mataram on Discussion Let your ideas and
//                     suggestions be a part of PM Modi's Independence Day Speech
//                   </p>
//                 </div>
//               </li>
//               <li>
//                 <FaRegClock className="history-icon" />
//                 <div className="history-content-row">
//                   <span className="history-date">Sep 14, 2025</span>
//                   <p>Completed “Innovation Challenge”</p>
//                 </div>
//               </li>
//             </ul>
//           </div>
//         </div>
//       </div>
//     </>
//   );
// };

// export default Dashboard;

const Dashboard = () => {
  const [activeSection, setActiveSection] = useState("history");

  const activityData = [
    {
      id: 1,
      activity: "Poll",
      name: "Environmental Awareness Poll",
      mark: "10 / 10",
      certificate: "/certificates/poll.pdf",
    },
    {
      id: 2,
      activity: "Quiz",
      name: "National Science Quiz",
      mark: "9 / 10",
      certificate: "/certificates/quiz.pdf",
    },
  ];

  return (
    <div className="dashboard-container">
      {/* Left Section */}
      <div className="left-section">
        <div className="card card-das profile-card full-height">
          <div className="bc-sec-1">
            <div className="bc-sec-title">
              <h2>My Profile</h2>
            </div>
          </div>

          <img src="/img/image.png" alt="Profile" className="profile-img" />
          <h2 className="user-name">Joseph Kuruvila</h2>
          <h3 className="user-id">User ID: 12345</h3>
          <img src="/img/badge.png" alt="Badge" className="badge-img" />

          {/* Menu Section */}
          <ul className="menu-list">
            <li
              className={`menu-item ${
                activeSection === "activity" ? "active" : ""
              }`}
              onClick={() => setActiveSection("activity")}
            >
              <MdLocalActivity className="menu-icon" />
              My Activity
            </li>
            <li
              className={`menu-item ${
                activeSection === "edit" ? "active" : ""
              }`}
              onClick={() => setActiveSection("edit")}
            >
              <FaEdit className="menu-icon" />
              Edit Profile
            </li>
            <li className="menu-item">
              <FaTasks className="menu-icon" />
              Account Settings
            </li>
            <li className="menu-item">
              <FaTrophy className="menu-icon" />
              Skills and Interests
            </li>
            <li className="menu-item">
              <GrDocumentUpdate className="menu-icon" />
              Update Profile Picture
            </li>
            <li className="menu-item">
              <MdEmail className="menu-icon" />
              Update Email Id
            </li>
            <li className="menu-item">
              <FaMobileAlt className="menu-icon" />
              Update Mobile Number
            </li>
            <li className="menu-item">
              <RiLockPasswordLine className="menu-icon" />
              Update Password
            </li>
            <li className="menu-item">
              <MdNoAccounts className="menu-icon" />
              Deactivate Account
            </li>
            <li className="menu-item">
              <FaShareSquare className="menu-icon" />
              Referral Code
            </li>
          </ul>
        </div>
      </div>

      {/* Right Section */}
      <div className="right-section">
        <div className="stats-cards">
          <div className="card card-das stat-card">
            <div className="icon-bg">
              <img src="/img/point.svg" alt="Badge" />
            </div>
            <p className="card-1">520</p>
            <h4>Total Points</h4>
          </div>

          <div className="card card-das stat-card">
            <div className="icon-bg">
              <img src="/img/level.svg" alt="Badge" />
            </div>
            <p className="card-2">Master</p>
            <h4>Level</h4>
          </div>

          <div className="card card-das stat-card">
            <div className="icon-bg">
              <img src="/img/puzzle.svg" alt="Badge" />
            </div>
            <p className="card-3">10</p>
            <h4>Activities</h4>
          </div>
        </div>

        {/* Conditional Rendering for Right Section */}
        {activeSection === "activity" ? (
          <div className="card card-das activity-table-card">
            <h3>My Activities</h3>
            <table className="activity-table">
              <thead>
                <tr>
                  <th>Sl No</th>
                  <th>Activity</th>
                  <th>Name</th>
                  <th>Mark</th>
                  <th>Certificate</th>
                </tr>
              </thead>
              {/* <tbody>
                {activityData.map((item, index) => (
                  <tr key={item.id}>
                    <td>{index + 1}</td>
                    <td>{item.activity}</td>
                    <td>{item.name}</td>
                    <td>{item.mark}</td>
                    <td>
                      <button
                        className="download-btn"
                        onClick={() => window.open(item.certificate, "_blank")}
                      >
                        Download
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody> */}
              <tbody>
                {activityData.map((item, index) => (
                  <tr key={item.id}>
                    <td data-label="Sl No">{index + 1}</td>
                    <td data-label="Activity">{item.activity}</td>
                    <td data-label="Name">{item.name}</td>
                    <td data-label="Mark">{item.mark}</td>
                    <td data-label="Certificate">
                      <button
                        className="download-btn"
                        onClick={() => window.open(item.certificate, "_blank")}
                      >
                        <FaFileDownload className="mb-1" /> Download
                      </button>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        ) : (
          <div className="card card-das history-card">
            <h3>History</h3>
            <ul>
              <li>
                <FaRegClock className="history-icon" />
                <div className="history-content-row">
                  <span className="history-date">Oct 6, 2025</span>
                  <p>Participated in National Quiz</p>
                </div>
              </li>
              <li>
                <FaAward className="history-icon" />
                <div className="history-content-row">
                  <span className="history-date">Oct 06, 2025</span>
                  <p>
                    Task accepted: International Year of Cooperatives 2025 Essay
                    Competition
                  </p>
                </div>
              </li>
              <li>
                <FaVideo className="history-icon" />
                <div className="history-content-row">
                  <span className="history-date">Aug 30, 2025</span>
                  <p>
                    Task accepted: Reel Making Contest on Emerging STI for
                    Viksit Bharat
                  </p>
                </div>
              </li>
              <li>
                <FaRegComment className="history-icon" />
                <div className="history-content-row">
                  <span className="history-date">Aug 14, 2024</span>
                  <p>
                    Replied “Good info” on discussion “Express your Patriotism”
                  </p>
                </div>
              </li>
              <li>
                <FaThumbsUp className="history-icon" />
                <div className="history-content-row">
                  <span className="history-date">Aug 14, 2024</span>
                  <p>Liked a comment on “Express your Patriotism”</p>
                </div>
              </li>
              <li>
                <FaRegEdit className="history-icon" />
                <div className="history-content-row">
                  <span className="history-date">Aug 09, 2024</span>
                  <p>
                    Posted “Vande Mataram” on discussion about Independence Day
                    Speech ideas
                  </p>
                </div>
              </li>
              <li>
                <FaRegClock className="history-icon" />
                <div className="history-content-row">
                  <span className="history-date">Sep 14, 2025</span>
                  <p>Completed “Innovation Challenge”</p>
                </div>
              </li>
            </ul>
          </div>
        )}
      </div>
    </div>
  );
};

export default Dashboard;
