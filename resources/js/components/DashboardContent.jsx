import { useState, useEffect } from "react";
import { useAuth } from "./App";
import { generateQuizCertificate, generatePledgeCertificate, generateTaskCertificate, generateCertificate } from "../utils/certificateGenerator";
import DashboardLayout from "./DashboardLayout";
import dashboardAPI from "../services/dashboardAPI";
import { FaRegClock, FaStar, FaTrophy, FaTasks, FaAward, FaVideo, FaRegComment, FaThumbsUp, FaRegEdit, FaFileDownload } from "react-icons/fa";

export default function DashboardContent() {
    const [dashboardData, setDashboardData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [generatingId, setGeneratingId] = useState(null);
    const { user } = useAuth?.() || {};

    useEffect(() => {
        fetchDashboardData();
    }, []);

    const fetchDashboardData = async () => {
        try {
            setLoading(true);
            const response = await dashboardAPI.getDashboard();
            setDashboardData(response.data);
        } catch (err) {
            console.error('Failed to fetch dashboard data:', err);
        } finally {
            setLoading(false);
        }
    };

    const activityData = dashboardData?.activities || [];
    const historyData = dashboardData?.history || [];

    const userName = user?.name || dashboardData?.user?.name || 'Participant';

    const generateActivityCertificate = async (item) => {
        try {
            setGeneratingId(item.id);
            const activityType = (item.activity || '').toLowerCase();
            if (activityType === 'quiz') {
                // mark may represent score; total questions unknown so pass mark as score for now
                await generateQuizCertificate(userName, item.name, item.mark, item.totalQuestions || item.max_score || 0);
            } else if (activityType === 'pledge') {
                await generatePledgeCertificate(userName, item.name);
            } else if (activityType === 'task') {
                await generateTaskCertificate(userName, item.name);
            } else {
                await generateCertificate({ userName, eventName: item.name, eventType: item.activity });
            }
        } catch (e) {
            console.error('Certificate generation failed:', e);
            alert('Failed to generate certificate. Please try again.');
        } finally {
            setGeneratingId(null);
        }
    };

    const renderPointsDisplay = (item, isQuiz) => {
        if (isQuiz && item.points_breakdown) {
            const breakdown = item.points_breakdown;

            return (
                <div className="points-badges">
                    <span className="points-badge badge-score">
                        Score: {breakdown.score}/{breakdown.max_score}
                    </span>
                    <span className="points-badge badge-participation">
                        Participation: {breakdown.participation}
                    </span>
                    <span className="points-badge badge-total">
                        Total: {item.points_earned ?? 0}
                    </span>
                </div>
            );
        }

        if (item.points_earned !== null && item.points_earned !== undefined) {
            return (
                <span className="points-badge badge-total">
                    Points: {item.points_earned}
                </span>
            );
        }

        return item.mark || 0;
    };

    if (loading) {
        return (
            <DashboardLayout>
                <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '400px' }}>
                    <div className="spinner-border text-primary" role="status">
                        <span className="visually-hidden">Loading...</span>
                    </div>
                </div>
            </DashboardLayout>
        );
    }

    return (
        <DashboardLayout>
            <style>{`
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
                    overflow: hidden;
                }

                .stat-card::before {
                    content: "";
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 4px;
                    background: linear-gradient(90deg, #039 0%, #00a859 100%);
                    opacity: 0.9;
                }

                .stat-card h4 {
                    font-size: 16px;
                    color: #475569;
                    margin: 0;
                }

                .stat-card p {
                    font-size: 34px;
                    font-weight: 600;
                    margin: 10px 0;
                    color: #0f172a;
                    letter-spacing: -0.02em;
                }

                .stat-card .card-1 { color: #039; }
                .stat-card .card-2 { color: #0c9; }
                .stat-card .card-3 { color: #09f; }

                .icon-bg {
                    position: absolute;
                    width: 50px;
                    right: 10px;
                    bottom: 10px;
                    opacity: 0.2;
                    filter: grayscale(30%);
                }

                .history-card, .activity-table-card {
                    grid-column: span 2;
                }

                .history-card h3, .activity-table-card h3 {
                    margin-bottom: 15px;
                    color: #0f172a;
                    font-size: 20px;
                }

                .activity-table-wrapper {
                    width: 100%;
                    border-radius: 12px;
                    border: 1px solid rgba(2, 6, 23, 0.08);
                }

                .activity-list {
                    display: flex;
                    flex-direction: column;
                    gap: 12px;
                    padding: 15px;
                }

                .activity-item {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 10px;
                    padding: 15px;
                    border: 1px solid rgba(2, 6, 23, 0.08);
                    border-left: 4px solid #039;
                    border-radius: 8px;
                }

                .activity-item:hover {
                    background: rgba(0, 51, 153, 0.03);
                }

                .activity-item-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .activity-label {
                    font-size: 12px;
                    font-weight: 600;
                    color: #64748b;
                    text-transform: uppercase;
                    letter-spacing: 0.5px;
                }

                .activity-value {
                    font-size: 14px;
                    color: #0f172a;
                    font-weight: 500;
                }

                .activity-table {
                    display: none;
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
                    border: 1px solid rgba(2, 6, 23, 0.08);
                    border-radius: 8px;
                }

                .history-card li:hover {
                    background: rgba(0, 51, 153, 0.06);
                }

                .history-icon {
                    color: #039;
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
                    color: #64748b;
                    margin-bottom: 2px;
                }

                .history-content-row p {
                    margin: 0;
                    font-size: 14px;
                    color: #0f172a;
                }

                @media (min-width: 768px) {
                    .activity-table {
                        display: table;
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
                        font-size: 14px;
                    }

                    .activity-table td {
                        padding: 12px;
                        border-bottom: 1px solid #e0e0e0;
                        color: #0f172a;
                        font-size: 14px;
                    }

                    .activity-table tbody tr:hover {
                        background: rgba(2, 6, 23, 0.02);
                    }

                    .activity-list {
                        display: none;
                    }
                }

                .download-btn {
                    background: #039;
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
                    background: #002a80;
                }

                .download-btn:disabled {
                    opacity: 0.7;
                    cursor: not-allowed;
                }

                .points-badges {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 6px;
                }

                .points-badge {
                    display: inline-flex;
                    align-items: center;
                    padding: 4px 8px;
                    border-radius: 999px;
                    font-size: 12px;
                    font-weight: 600;
                    line-height: 1;
                    color: #0f172a;
                    background: #e2e8f0;
                }

                .badge-score {
                    background: #e0f2fe;
                    color: #075985;
                }

                .badge-participation {
                    background: #e0f2fe;
                    color: #075985;
                }

                .badge-total {
                    background: #0c9;
                    color: #fff;
                }

                @media (max-width: 1024px) {
                    .stats-cards {
                        grid-template-columns: repeat(3, 1fr);
                    }
                }

                @media (max-width: 768px) {
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

                    .activity-table {
                        min-width: 640px;
                    }
                }
            `}</style>

            <div className="stats-cards">
                <div className="card card-das stat-card">
                    <div className="icon-bg">
                        <img src="/design/assets/point.svg" alt="Points" />
                    </div>
                    <p className="card-1">{dashboardData?.stats?.total_points || 0}</p>
                    <h4 className="font-weight-bold">Total Points</h4>
                </div>

                <div className="card card-das stat-card">
                    <div className="icon-bg">
                        <img src="/design/assets/level.svg" alt="Level" />
                    </div>
                    <p className="card-2">{dashboardData?.stats?.badge?.name || 'Beginner'}</p>
                    <h4 className="font-weight-bold">Level</h4>
                </div>

                <div className="card card-das stat-card">
                    <div className="icon-bg">
                        <img src="/design/assets/puzzle.svg" alt="Activities" />
                    </div>
                    <p className="card-3">{dashboardData?.stats?.activities_count || 0}</p>
                    <h4 className="font-weight-bold">Activities</h4>
                </div>
            </div>

            <div className="card card-das activity-table-card">
                <h3>Recent Activities</h3>
                {activityData.length > 0 ? (
                    <>
                        {/* Desktop Table View */}
                        <div className="activity-table-wrapper">
                            <table className="activity-table">
                                <thead>
                                    <tr>
                                        <th>Sl No</th>
                                        <th>Activity</th>
                                        <th>Name</th>
                                        <th>Points</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {activityData.map((item, index) => {
                                        const activityTypeRaw = (item.activity || item.activity_type || '').toString().toLowerCase();
                                        const normalizedActivity = activityTypeRaw.replace(/[_\s]+/g, '-');
                                        const isProfileCompletion = normalizedActivity.includes('profile') && normalizedActivity.includes('completion');
                                        const isQuiz = activityTypeRaw.includes('quiz');
                                        
                                        const pointsDisplay = renderPointsDisplay(item, isQuiz);

                                        return (
                                            <tr key={item.id}>
                                                <td>{index + 1}</td>
                                                <td>{item.activity}</td>
                                                <td>{item.name}</td>
                                                <td>{pointsDisplay}</td>
                                            </tr>
                                        );
                                    })}
                                </tbody>
                            </table>
                        </div>

                        {/* Mobile Card View */}
                        <div className="activity-list">
                            {activityData.map((item, index) => {
                                const activityTypeRaw = (item.activity || item.activity_type || '').toString().toLowerCase();
                                const normalizedActivity = activityTypeRaw.replace(/[_\s]+/g, '-');
                                const isProfileCompletion = normalizedActivity.includes('profile') && normalizedActivity.includes('completion');
                                const isQuiz = activityTypeRaw.includes('quiz');
                                
                                // Format points display
                                const pointsDisplay = renderPointsDisplay(item, isQuiz);

                                return (
                                    <div key={item.id} className="activity-item">
                                        <div className="activity-item-row">
                                            <div>
                                                <div className="activity-label">Sl No</div>
                                                <div className="activity-value">{index + 1}</div>
                                            </div>
                                            <div>
                                                <div className="activity-label">Activity</div>
                                                <div className="activity-value">{item.activity}</div>
                                            </div>
                                        </div>
                                        <div className="activity-item-row">
                                            <div style={{flex: 1}}>
                                                <div className="activity-label">Name</div>
                                                <div className="activity-value">{item.name}</div>
                                            </div>
                                            <div>
                                                <div className="activity-label">Points</div>
                                                <div className="activity-value">{pointsDisplay}</div>
                                            </div>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    </>
                ) : (
                    <p style={{ textAlign: 'center', padding: '20px', color: '#999' }}>
                        No activities yet. Start participating to see your activities here!
                    </p>
                )}
            </div>

            <div className="card card-das history-card">
                <h3>Activity History</h3>
                {historyData.length > 0 ? (
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
                ) : (
                    <p style={{ textAlign: 'center', padding: '20px', color: '#999' }}>
                        No history yet. Your activities will appear here.
                    </p>
                )}
            </div>
        </DashboardLayout>
    );
}
