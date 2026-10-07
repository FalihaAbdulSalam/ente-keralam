import { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { useAuth } from "./App";
import { useLanguage } from "./LanguageContext";
import dashboardAPI from "../services/dashboardAPI";
import UserAvatar from "./UserAvatar";
import { FaRegClock, FaStar, FaTrophy, FaTasks, FaEdit, FaShareSquare, FaMobileAlt, FaAward, FaVideo, FaRegComment, FaThumbsUp, FaRegEdit, FaFileDownload } from "react-icons/fa";
import { FiSettings } from "react-icons/fi";
import { GrDocumentUpdate } from "react-icons/gr";
import { MdNoAccounts, MdEmail, MdLocalActivity } from "react-icons/md";
import { RiLockPasswordLine } from "react-icons/ri";

export default function Dashboard() {
    const { user, logout } = useAuth();
    const { language } = useLanguage();
    const navigate = useNavigate();
    const [activeSection, setActiveSection] = useState("history");
    const [loading, setLoading] = useState(true);
    const [dashboardData, setDashboardData] = useState(null);
    const [error, setError] = useState(null);

    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === 'ml' ? (malText || enText) : enText;
    };

    useEffect(() => {
        fetchDashboardData();
    }, []);

    const fetchDashboardData = async () => {
        try {
            setLoading(true);
            const response = await dashboardAPI.getDashboard();
            setDashboardData(response.data);
            setError(null);
        } catch (err) {
            console.error('Failed to fetch dashboard data:', err);
            setError('Failed to load dashboard data');
        } finally {
            setLoading(false);
        }
    };

    const getIconForActivity = (activityType) => {
        switch (activityType) {
            case 'quiz':
                return <FaStar className="history-icon" />;
            case 'pledge':
                return <FaAward className="history-icon" />;
            case 'poll':
                return <FaTasks className="history-icon" />;
            case 'competition':
                return <FaTrophy className="history-icon" />;
            case 'task':
                return <FaRegEdit className="history-icon" />;
            case 'discussion':
                return <FaRegComment className="history-icon" />;
            default:
                return <FaRegClock className="history-icon" />;
        }
    };

    // Use real data from API or fallback to empty arrays
    const activityData = dashboardData?.activities || [];
    const historyData = dashboardData?.history || [];

    const menuItems = [
        {
            section: "activity",
            icon: <MdLocalActivity className="menu-icon" />,
            label: "My Activity"
        },
        {
            section: "edit",
            icon: <FaEdit className="menu-icon" />,
            label: "Edit Profile",
            onClick: () => navigate('/profile')
        },
        {
            section: null,
            icon: <FaTasks className="menu-icon" />,
            label: "Account Settings"
        },
        {
            section: null,
            icon: <FaTrophy className="menu-icon" />,
            label: "Skills and Interests"
        },
        {
            section: null,
            icon: <GrDocumentUpdate className="menu-icon" />,
            label: "Update Profile Picture"
        },
        {
            section: null,
            icon: <MdEmail className="menu-icon" />,
            label: "Update Email Id"
        },
        {
            section: null,
            icon: <FaMobileAlt className="menu-icon" />,
            label: "Update Mobile Number"
        },
        {
            section: null,
            icon: <RiLockPasswordLine className="menu-icon" />,
            label: "Update Password"
        },
        {
            section: null,
            icon: <MdNoAccounts className="menu-icon" />,
            label: "Deactivate Account"
        },
        {
            section: null,
            icon: <FaShareSquare className="menu-icon" />,
            label: "Referral Code"
        },
    ];

    if (loading) {
        return (
            <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '60vh' }}>
                <div className="spinner-border text-primary" role="status">
                    <span className="visually-hidden">Loading...</span>
                </div>
            </div>
        );
    }

    return (
        <>
            <style>{`
                .dashboard-container {
                    display: grid;
                    grid-template-columns: 1fr 4fr;
                    gap: 20px;
                    padding: 30px;
                    background: #f8faff;
                    min-height: 80vh;
                }

                .card-das {
                    background: rgba(255, 255, 255, 0.85);
                    border-radius: 15px !important;
                    padding: 20px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    backdrop-filter: blur(10px);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .card-das:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.1);
                }

                .profile-card {
                    align-items: center;
                    text-align: center;
                    display: flex;
                    flex-direction: column;
                }

                .profile-img {
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;
                    object-fit: cover;
                    margin-bottom: 10px;
                    border: 3px solid #4b7bec;
                    margin-top: 35px;
                    background: #e0e0e0;
                }

                .user-name {
                    font-size: 23px;
                    color: #333;
                    margin-bottom: 5px;
                }

                .user-id {
                    font-size: 16px;
                    color: #666;
                    margin-bottom: 15px;
                }

                .badge-img {
                    width: 50px;
                    height: auto;
                }

                .badge-name {
                    font-size: 14px;
                    color: #666;
                    margin-top: 5px;
                    font-weight: 600;
                }

                .menu-list {
                    list-style: none;
                    padding: 0;
                    margin-top: 25px;
                    width: 100%;
                }

                .menu-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    padding: 7px 0px;
                    color: #333;
                    font-size: 15px;
                    border-radius: 10px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    text-align: left;
                    line-height: normal;
                }

                .menu-item:hover {
                    color: #d9534f;
                }

                .menu-item.active {
                    color: #d9534f;
                    font-weight: 600;
                }

                .menu-icon {
                    font-size: 18px;
                    min-width: 18px;
                }

                .right-section {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }

                .stats-cards {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 20px;
                }

                .stat-card {
                    position: relative;
                    text-align: center;
                    padding: 30px 20px !important;
                }

                .stat-card h4 {
                    font-size: 16px;
                    color: #555;
                    margin: 0;
                }

                .stat-card p {
                    font-size: 38px;
                    font-weight: 600;
                    margin: 10px 0;
                }

                .stat-card .card-1 { color: #4b7bec; }
                .stat-card .card-2 { color: #26de81; }
                .stat-card .card-3 { color: #fd79a8; }

                .icon-bg {
                    position: absolute;
                    width: 50px;
                    right: 10px;
                    bottom: 10px;
                    opacity: 0.7;
                }

                .history-card, .activity-table-card {
                    grid-column: span 2;
                }

                .history-card h3, .activity-table-card h3 {
                    margin-bottom: 15px;
                    color: #333;
                    font-size: 20px;
                }

                .history-card ul {
                    list-style: none;
                    padding: 0;
                    margin: 0;
                }

                .history-card li {
                    display: flex;
                    align-items: flex-start;
                    background: transparent;
                    margin-bottom: 10px;
                    padding: 10px 15px;
                    border: 1px solid rgba(54, 91, 173, 0.26);
                    border-radius: 8px;
                }

                .history-card li:hover {
                    background: #e5ebff;
                }

                .history-icon {
                    color: #4b7bec;
                    font-size: 20px;
                    margin-right: 12px;
                    min-width: 20px;
                }

                .history-content-row {
                    display: flex;
                    flex-direction: column;
                    flex: 1;
                }

                .history-date {
                    font-size: 12px;
                    color: #777;
                    margin-bottom: 2px;
                }

                .history-content-row p {
                    margin: 0;
                    font-size: 14px;
                    color: #333;
                }

                .activity-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 15px;
                }

                .activity-table th {
                    background: #039;
                    color: white;
                    padding: 12px;
                    text-align: left;
                    font-weight: 600;
                }

                .activity-table td {
                    padding: 12px;
                    border-bottom: 1px solid #e0e0e0;
                }

                .activity-table tbody tr:hover {
                    background: #f5f7fa;
                }

                .download-btn {
                    background: #26de81;
                    color: white;
                    border: none;
                    padding: 6px 15px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                    display: flex;
                    align-items: center;
                    gap: 5px;
                    transition: all 0.2s ease;
                }

                .download-btn:hover {
                    background: #20bf6b;
                }

                @media (max-width: 1024px) {
                    .dashboard-container {
                        grid-template-columns: 1fr;
                    }

                    .stats-cards {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }

                @media (max-width: 768px) {
                    .dashboard-container {
                        padding: 15px;
                    }

                    .stats-cards {
                        grid-template-columns: 1fr;
                    }

                    .activity-table {
                        font-size: 14px;
                    }

                    .activity-table th,
                    .activity-table td {
                        padding: 8px;
                    }
                }
            `}</style>

            <div className="dashboard-container">
                {/* Left Section */}
                <div className="left-section">
                    <div className="card card-das profile-card">
                        <div className="bc-sec-1">
                            <div className="bc-sec-title">
                                <h2>My Profile</h2>
                            </div>
                        </div>

                        <UserAvatar 
                            user={user} 
                            size={120} 
                            fontSize={48}
                            className="profile-img"
                        />
                        <h2 className="user-name">{user?.name || 'User'}</h2>
                        <h3 className="user-id">User ID: {user?.id || '12345'}</h3>
                        {dashboardData?.stats?.badge ? (
                            <>
                                <img 
                                    src={dashboardData.stats.badge.icon} 
                                    alt={dashboardData.stats.badge.name} 
                                    className="badge-img" 
                                    title={`${dashboardData.stats.badge.name} Badge`}
                                />
                                <p className="badge-name" style={{ color: dashboardData.stats.badge.color }}>
                                    {dashboardData.stats.badge.name}
                                </p>
                            </>
                        ) : (
                            <>
                                <img 
                                    src="/design/assets/badges/beginner.png" 
                                    alt="Beginner Badge" 
                                    className="badge-img" 
                                />
                                <p className="badge-name">Beginner</p>
                            </>
                        )}

                        {/* Menu Section */}
                        <ul className="menu-list">
                            {menuItems.map((item, index) => (
                                <li
                                    key={index}
                                    className={`menu-item ${activeSection === item.section ? 'active' : ''}`}
                                    onClick={() => {
                                        if (item.onClick) {
                                            item.onClick();
                                        } else if (item.section) {
                                            setActiveSection(item.section);
                                        }
                                    }}
                                >
                                    {item.icon}
                                    {item.label}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>

                {/* Right Section */}
                <div className="right-section">
                    <div className="stats-cards">
                        <div className="card card-das stat-card">
                            <div className="icon-bg">
                                <img src="/design/assets/point.svg" alt="Points" />
                            </div>
                            <p className="card-1">{dashboardData?.stats?.total_points || 0}</p>
                            <h4>Total Points</h4>
                        </div>

                        <div className="card card-das stat-card">
                            <div className="icon-bg">
                                <img src="/design/assets/level.svg" alt="Level" />
                            </div>
                            <p className="card-2">{dashboardData?.stats?.badge?.name || 'Beginner'}</p>
                            <h4>Level</h4>
                        </div>

                        <div className="card card-das stat-card">
                            <div className="icon-bg">
                                <img src="/design/assets/puzzle.svg" alt="Activities" />
                            </div>
                            <p className="card-3">{dashboardData?.stats?.activities_count || 0}</p>
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
                                <tbody>
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
                                                    <FaFileDownload /> Download
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
                                {historyData.map((item, index) => (
                                    <li key={index}>
                                        {item.icon !== 'default' ? (
                                            <img src={item.icon} alt="icon" className="history-icon" style={{ width: '20px', height: '20px' }} />
                                        ) : (
                                            <FaRegClock className="history-icon" />
                                        )}
                                        <div className="history-content-row">
                                            <span className="history-date">{item.date}</span>
                                            <p>{item.text}</p>
                                        </div>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            </div>
        </>
    );
}
