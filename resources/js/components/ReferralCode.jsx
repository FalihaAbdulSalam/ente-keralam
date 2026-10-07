import { useState, useEffect } from 'react';
import DashboardLayout from './DashboardLayout';
import dashboardAPI from '../services/dashboardAPI';
import { FaCopy, FaWhatsapp, FaFacebook, FaTwitter, FaEnvelope, FaUsers, FaTrophy } from 'react-icons/fa';

export default function ReferralCode() {
    const [referralData, setReferralData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [copied, setCopied] = useState(false);

    useEffect(() => {
        fetchReferralData();
    }, []);

    const fetchReferralData = async () => {
        try {
            const response = await dashboardAPI.getReferralCode();
            setReferralData(response.data);
        } catch (err) {
            console.error('Failed to fetch referral data:', err);
        } finally {
            setLoading(false);
        }
    };

    const copyToClipboard = () => {
        navigator.clipboard.writeText(referralData.code);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    };

    const shareViaWhatsApp = () => {
        const url = `${window.location.origin}/register?ref=${encodeURIComponent(referralData.code)}`;
        const message = `Join Ente Keralam using my referral code: ${referralData.code}\n\nRegister here: ${url}`;
        window.open(`https://wa.me/?text=${encodeURIComponent(message)}`, '_blank');
    };

    const shareViaFacebook = () => {
        const url = `${window.location.origin}/register?ref=${encodeURIComponent(referralData.code)}`;
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`, '_blank');
    };

    const shareViaTwitter = () => {
        const url = `${window.location.origin}/register?ref=${encodeURIComponent(referralData.code)}`;
        const message = `Join Ente Keralam using my referral code: ${referralData.code}\n${url}`;
        window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(message)}`, '_blank');
    };

    const shareViaEmail = () => {
        const subject = 'Join me on Ente Keralam!';
        const body = `Hi,\n\nI'd like to invite you to join Ente Keralam. Use my referral code: ${referralData.code}\n\nRegister here: ${window.location.origin}/register?ref=${encodeURIComponent(referralData.code)}`;
        window.location.href = `mailto:?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
    };

    if (loading) {
        return (
            <DashboardLayout>
                <div className="d-flex justify-content-center align-items-center" style={{ minHeight: '60vh' }}>
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
                .referral-wrapper {
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .referral-header {
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    text-align: center;
                    margin-bottom: 30px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    border-left: 4px solid #4b7bec;
                }

                .referral-header h2 {
                    font-size: 28px;
                    margin-bottom: 10px;
                    font-weight: 600;
                    color: #333;
                }

                .referral-header p {
                    font-size: 16px;
                    color: #666;
                    margin: 0;
                }

                .referral-code-card {
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    margin-bottom: 30px;
                }

                .referral-code-display {
                    background: #f8f9fa;
                    border: 2px dashed #4b7bec;
                    border-radius: 10px;
                    padding: 25px;
                    text-align: center;
                    margin-bottom: 20px;
                }

                .referral-code-text {
                    font-size: 36px;
                    font-weight: 700;
                    color: #333;
                    letter-spacing: 5px;
                    margin-bottom: 10px;
                }

                .copy-button {
                    background: #039;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    transition: background 0.2s;
                }

                .copy-button:hover {
                    background: #11306f;
                }

                .copy-button.copied {
                    background: #27ae60;
                }

                .share-section {
                    margin-top: 30px;
                }

                .share-section h3 {
                    font-size: 20px;
                    color: #333;
                    margin-bottom: 20px;
                    font-weight: 600;
                }

                .share-buttons {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    gap: 15px;
                }

                .share-btn {
                    padding: 15px 20px;
                    border: none;
                    border-radius: 8px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 10px;
                    transition: all 0.3s ease;
                    color: white;
                }

                .share-btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
                }

                .share-btn.whatsapp {
                    background: #25D366;
                }

                .share-btn.facebook {
                    background: #1877F2;
                }

                .share-btn.twitter {
                    background: #1DA1F2;
                }

                .share-btn.email {
                    background: #EA4335;
                }

                .stats-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                    gap: 20px;
                    margin-bottom: 30px;
                }

                .stat-card {
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    text-align: center;
                }

                .stat-icon {
                    font-size: 48px;
                    margin-bottom: 15px;
                }

                .stat-icon.users {
                    color: #4b7bec;
                }

                .stat-icon.points {
                    color: #f5a623;
                }

                .stat-value {
                    font-size: 36px;
                    font-weight: 700;
                    color: #333;
                    margin-bottom: 5px;
                }

                .stat-label {
                    font-size: 16px;
                    color: #666;
                    font-weight: 500;
                }

                .referrals-list-card {
                    background: white;
                    border-radius: 15px;
                    padding: 30px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                }

                .referrals-list-card h3 {
                    font-size: 24px;
                    color: #333;
                    margin-bottom: 20px;
                    font-weight: 600;
                }

                .referral-item {
                    padding: 15px;
                    border-bottom: 1px solid #e0e0e0;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .referral-item:last-child {
                    border-bottom: none;
                }

                .referral-info {
                    flex: 1;
                }

                .referral-name {
                    font-weight: 600;
                    color: #333;
                    margin-bottom: 5px;
                }

                .referral-email {
                    font-size: 14px;
                    color: #666;
                }

                .referral-points {
                    background: #4b7bec;
                    color: white;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-weight: 600;
                    font-size: 14px;
                }

                .no-referrals {
                    text-align: center;
                    padding: 40px;
                    color: #999;
                }

                @media (max-width: 768px) {
                    .referral-header {
                        padding: 30px 20px;
                    }

                    .referral-header h2 {
                        font-size: 24px;
                    }

                    .referral-code-text {
                        font-size: 28px;
                        letter-spacing: 3px;
                    }

                    .share-buttons {
                        grid-template-columns: 1fr;
                    }

                    .stats-grid {
                        grid-template-columns: 1fr;
                    }
                }
            `}</style>

            <div className="referral-wrapper">
                <div className="referral-header">
                    <h2>Invite Friends & Earn Rewards!</h2>
                    <p>Share your referral code and earn 50 points for each friend who joins</p>
                </div>

                <div className="stats-grid">
                    <div className="stat-card">
                        <div className="stat-icon users">
                            <FaUsers />
                        </div>
                        <div className="stat-value">{referralData.referral_count}</div>
                        <div className="stat-label">Total Referrals</div>
                    </div>

                    <div className="stat-card">
                        <div className="stat-icon points">
                            <FaTrophy />
                        </div>
                        <div className="stat-value">{referralData.points_earned}</div>
                        <div className="stat-label">Points Earned</div>
                    </div>
                </div>

                <div className="referral-code-card">
                    <div className="referral-code-display">
                        <div className="referral-code-text">{referralData.code}</div>
                        <button 
                            className={`copy-button ${copied ? 'copied' : ''}`}
                            onClick={copyToClipboard}
                        >
                            <FaCopy />
                            {copied ? 'Copied!' : 'Copy Code'}
                        </button>
                    </div>

                    <div className="share-section">
                        <h3>Share via</h3>
                        <div className="share-buttons">
                            <button className="share-btn whatsapp" onClick={shareViaWhatsApp}>
                                <FaWhatsapp size={20} />
                                WhatsApp
                            </button>
                            <button className="share-btn facebook" onClick={shareViaFacebook}>
                                <FaFacebook size={20} />
                                Facebook
                            </button>
                            <button className="share-btn twitter" onClick={shareViaTwitter}>
                                <FaTwitter size={20} />
                                Twitter
                            </button>
                            <button className="share-btn email" onClick={shareViaEmail}>
                                <FaEnvelope size={20} />
                                Email
                            </button>
                        </div>
                    </div>
                </div>

                {referralData.referrals && referralData.referrals.length > 0 && (
                    <div className="referrals-list-card">
                        <h3>Your Referrals ({referralData.referrals.length})</h3>
                        {referralData.referrals.map((referral, index) => (
                            <div key={index} className="referral-item">
                                <div className="referral-info">
                                    <div className="referral-name">{referral.name}</div>
                                    <div className="referral-email">{referral.email}</div>
                                </div>
                                <div className="referral-points">+{referral.points_awarded} pts</div>
                            </div>
                        ))}
                    </div>
                )}

                {referralData.referrals && referralData.referrals.length === 0 && (
                    <div className="referrals-list-card">
                        <div className="no-referrals">
                            <p>No referrals yet. Start sharing your code to earn points!</p>
                        </div>
                    </div>
                )}
            </div>
        </DashboardLayout>
    );
}
