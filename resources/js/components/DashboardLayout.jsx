import { useState, useEffect } from "react";
import { useNavigate, useLocation } from "react-router-dom";
import { useAuth } from "./App";
import dashboardAPI from "../services/dashboardAPI";
import UserAvatar from "./UserAvatar";
import { FaTasks, FaEdit, FaShareSquare, FaMobileAlt, FaTrophy, FaSignOutAlt } from "react-icons/fa";
import { FiSettings } from "react-icons/fi";
import { GrDocumentUpdate } from "react-icons/gr";
import { MdNoAccounts, MdEmail, MdLocalActivity } from "react-icons/md";
import { RiLockPasswordLine } from "react-icons/ri";

export default function DashboardLayout({ children }) {
    const { user, logout } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const [dashboardData, setDashboardData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [navigating, setNavigating] = useState(false);
    const [referralCode, setReferralCode] = useState('');
    const [referralCopied, setReferralCopied] = useState(false);

    const referralCacheKey = user?.id ? `referral_code_user_${user.id}` : 'referral_code_user_unknown';

    useEffect(() => {
        fetchDashboardData();
        // Try cache first to avoid repeated calls
        try {
            const cached = user?.id ? localStorage.getItem(referralCacheKey) : null;
            if (cached) {
                setReferralCode(cached);
            } else {
                fetchReferralCode();
            }
        } catch (e) {
            // If localStorage is blocked, fall back to API
            fetchReferralCode();
        }
    }, []);

    // Show a brief overlay during route changes to avoid perceived UI "jerk"
    useEffect(() => {
        setNavigating(true);
        const t = setTimeout(() => setNavigating(false), 500);
        return () => clearTimeout(t);
    }, [location.pathname]);

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

    const fetchReferralCode = async () => {
        try {
            const response = await dashboardAPI.getReferralCode();
            const code = response?.data?.code || '';
            setReferralCode(code);
            if (code && user?.id) {
                try {
                    localStorage.setItem(referralCacheKey, code);
                } catch (e) {
                    // ignore cache write failures
                }
            }
        } catch (err) {
            // Don't block dashboard rendering if referral API fails
            console.warn('Failed to fetch referral code:', err);
        }
    };

    const copyReferralCode = async () => {
        if (!referralCode) return;
        try {
            await navigator.clipboard.writeText(referralCode);
            setReferralCopied(true);
            setTimeout(() => setReferralCopied(false), 1500);
        } catch (e) {
            console.error('Failed to copy referral code:', e);
        }
    };

    const handleMenuClick = (item) => {
        if (item.action) {
            item.action();
            return;
        }
        if (location.pathname === item.path) return;
        setNavigating(true);
        navigate(item.path);
    };

    const handleLogout = async () => {
        try {
            await logout();
            try {
                if (user?.id) localStorage.removeItem(referralCacheKey);
            } catch (e) {
                // ignore cache clear failures
            }
            navigate('/');
        } catch (error) {
            console.error('Logout failed:', error);
        }
    };

    const menuItems = [
        {
            path: "/dashboard",
            icon: <MdLocalActivity className="menu-icon" />,
            label: "My Activity"
        },
        {
            path: "/profile/edit",
            icon: <FaEdit className="menu-icon" />,
            label: "Edit Profile",
        },
        /*
        {
            path: "/account-settings",
            icon: <FaTasks className="menu-icon" />,
            label: "Account Settings"
        },
        {
            path: "/skills-interests",
            icon: <FaTrophy className="menu-icon" />,
            label: "Skills and Interests"
        },
        */
        {
            path: "/update-picture",
            icon: <GrDocumentUpdate className="menu-icon" />,
            label: "Update Profile Picture"
        },
        {
            path: "/update-email",
            icon: <MdEmail className="menu-icon" />,
            label: "Update Email Id"
        },
        {
            path: "/update-mobile",
            icon: <FaMobileAlt className="menu-icon" />,
            label: "Update Mobile Number"
        },
        {
            path: "/update-password",
            icon: <RiLockPasswordLine className="menu-icon" />,
            label: "Update Password"
        },
        {
            path: "/deactivate",
            icon: <MdNoAccounts className="menu-icon" />,
            label: "Deactivate Account"
        },
        {
            path: "/referral",
            icon: <FaShareSquare className="menu-icon" />,
            label: "Referral Code"
        },
        {
            path: "#",
            icon: <FaSignOutAlt className="menu-icon" />,
            label: "Logout",
            action: handleLogout
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
                .dashboard-shell {
                    position: relative;
                }

                .dashboard-nav-overlay {
                    position: fixed;
                    inset: 0;
                    background: rgba(255, 255, 255, 0.6);
                    backdrop-filter: blur(2px);
                    z-index: 9999;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 160ms ease;
                }

                .dashboard-nav-overlay.show {
                    opacity: 1;
                    pointer-events: auto;
                }

                .dashboard-nav-overlay .spinner-border {
                    width: 2.25rem;
                    height: 2.25rem;
                    color: #039;
                }

                .dashboard-container {
                    display: grid;
                    grid-template-columns: 1fr 4fr;
                    gap: 20px;
                    padding: 30px;
                    background: #f5f7fb;
                    min-height: 80vh;
                }

                .left-section {
                    position: sticky;
                    top: 18px;
                    height: max-content;
                }

                .card-das {
                    background: rgba(255, 255, 255, 0.92);
                    border-radius: 16px !important;
                    padding: 20px;
                    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
                    backdrop-filter: blur(10px);
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                    border: 1px solid rgba(2, 6, 23, 0.06);
                }

                .card-das:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 14px 34px rgba(15, 23, 42, 0.12);
                }

                .profile-card {
                    align-items: center;
                    text-align: center;
                    display: flex;
                    flex-direction: column;
                }

                .profile-img {
                    margin-bottom: 10px;
                }

                .user-name {
                    font-size: 20px;
                    color: #0f172a;
                    margin-bottom: 5px;
                }

                .user-id {
                    font-size: 16px;
                    color: #64748b;
                    margin-bottom: 15px;
                }

                .referral-code {
                    cursor: pointer;
                    user-select: none;
                }

                .referral-code:hover {
                    color: #039;
                }

                .dashboard-toast {
                    position: fixed;
                    left: 50%;
                    bottom: 18px;
                    transform: translateX(-50%);
                    background: rgba(15, 23, 42, 0.92);
                    color: #fff;
                    padding: 10px 14px;
                    border-radius: 10px;
                    font-size: 13px;
                    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.2);
                    z-index: 10000;
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 160ms ease, transform 160ms ease;
                }

                .dashboard-toast.show {
                    opacity: 1;
                    transform: translateX(-50%) translateY(-4px);
                }

                .badge-img {
                    width: 65px;
                    height: auto;
                }

                .badge-name {
                    font-size: 14px;
                    color: #666;
                    margin-top: 5px;
                    font-weight: 600;
                }

                .completion-tracker {
                    background: rgba(2, 6, 23, 0.03);
                    padding: 10px 12px;
                    border-radius: 8px;
                    margin: 15px 0 20px 0;
                    width: 100%;
                }

                .completion-info {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 6px;
                }

                .completion-text {
                    font-size: 12px;
                    color: #475569;
                    font-weight: 600;
                }

                .completion-bar {
                    height: 6px;
                    background: #e0e0e0;
                    border-radius: 3px;
                    overflow: hidden;
                }

                .completion-progress {
                    height: 100%;
                    background: linear-gradient(90deg, #039 0%, #00a859 100%);
                    transition: width 0.3s ease;
                    border-radius: 3px;
                }

                .completion-reward {
                    font-size: 11px;
                    color: #27ae60;
                    font-weight: 600;
                    text-align: center;
                    margin-top: 5px;
                }

                .menu-list {
                    list-style: none;
                    padding: 0;
                    margin-top: 10px;
                    width: 100%;
                }

                .menu-item {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    padding: 10px 12px;
                    color: #0f172a;
                    font-size: 15px;
                    border-radius: 10px;
                    cursor: pointer;
                    transition: all 0.2s ease;
                    text-align: left;
                    line-height: normal;
                }

                .menu-item:hover {
                    background: rgba(0, 51, 153, 0.08);
                    color: #039;
                }

                .menu-item.active {
                    background: rgba(0, 51, 153, 0.12);
                    color: #039;
                    font-weight: 600;
                }

                .menu-icon {
                    font-size: 18px;
                    min-width: 18px;
                }

                @media (max-width: 1024px) {
                    .dashboard-container {
                        grid-template-columns: 1fr;
                    }

                    .left-section {
                        position: static;
                    }
                }

                @media (max-width: 768px) {
                    .dashboard-container {
                        padding: 15px;
                    }
                }
            `}</style>

            <div className="dashboard-shell">
                <div className={`dashboard-nav-overlay ${navigating ? 'show' : ''}`} aria-hidden={!navigating}>
                    <div className="spinner-border" role="status" aria-label="Loading" />
                </div>

                <div className={`dashboard-toast ${referralCopied ? 'show' : ''}`} role="status" aria-live="polite">
                    Referral code copied
                </div>

                <div className="dashboard-container">
                {/* Left Section - Sidebar */}
                <div className="left-section">
                    <div className="card card-das profile-card">
                        <div className="bc-sec-1">
                            <div className="bc-sec-title">
                                <h3>My Profile</h3>
                            </div>
                        </div>

                        {/* Profile Completion Tracker - At Top */}
                        {dashboardData?.stats && (Number(dashboardData.stats.profile_completion || 0) < 100) && (
                            <div className="completion-tracker">
                                <div className="completion-info">
                                    <span className="completion-text">
                                        Profile: {dashboardData.stats.profile_completion || 0}%
                                    </span>
                                </div>
                                <div className="completion-bar">
                                    <div 
                                        className="completion-progress" 
                                        style={{ width: `${dashboardData.stats.profile_completion || 0}%` }}
                                    ></div>
                                </div>
                            </div>
                        )}

                        <UserAvatar 
                            user={user} 
                            size={90} 
                            fontSize={48}
                            className="profile-img"
                        />
                        <h2 className="user-name">{user?.name || 'User'}</h2>
                        <h3
                            className={`user-id referral-code ${referralCopied ? 'copied' : ''}`}
                            title={referralCode ? 'Click to copy referral code' : ''}
                            onClick={copyReferralCode}
                        >
                            Referral Code: {referralCode || '—'}
                        </h3>
                        
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
                                    alt="Badge" 
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
                                    className={`menu-item ${location.pathname === item.path ? 'active' : ''}`}
                                    onClick={() => handleMenuClick(item)}
                                >
                                    {item.icon}
                                    {item.label}
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>

                {/* Right Section - Main Content */}
                <div className="right-section">
                    {children}
                </div>
            </div>
            </div>
        </>
    );
}
