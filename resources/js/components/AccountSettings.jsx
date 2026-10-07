import { useState, useEffect } from 'react';
import DashboardLayout from './DashboardLayout';
import dashboardAPI from '../services/dashboardAPI';
import { FaBell, FaEye, FaShieldAlt, FaEnvelope } from 'react-icons/fa';

export default function AccountSettings() {
    const [settings, setSettings] = useState({
        // Visibility Settings
        profile_visibility: 'public', // public, friends, private
        show_email: false,
        show_phone: false,
        show_activities: true,
        
        // Notification Settings
        email_notifications: true,
        task_reminders: true,
        poll_notifications: true,
        quiz_notifications: true,
        achievement_notifications: true,
        weekly_digest: false,
    });
    
    const [loading, setLoading] = useState(false);
    const [initialLoading, setInitialLoading] = useState(true);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState('');

    // Load settings on mount
    useEffect(() => {
        loadSettings();
    }, []);

    const loadSettings = async () => {
        try {
            setInitialLoading(true);
            const response = await dashboardAPI.getSettings();
            if (response.data.settings) {
                setSettings(response.data.settings);
            }
        } catch (err) {
            console.error('Failed to load settings:', err);
            setError('Failed to load settings. Using defaults.');
        } finally {
            setInitialLoading(false);
        }
    };

    const handleToggle = (field) => {
        setSettings(prev => ({
            ...prev,
            [field]: !prev[field]
        }));
    };

    const handleVisibilityChange = (value) => {
        setSettings(prev => ({
            ...prev,
            profile_visibility: value
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        setSuccess(false);

        try {
            const response = await dashboardAPI.updateSettings(settings);
            
            setSuccess(true);
            setTimeout(() => setSuccess(false), 3000);
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to update settings');
        } finally {
            setLoading(false);
        }
    };

    if (initialLoading) {
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
                .settings-wrapper {
                    background: rgba(255, 255, 255, 0.9);
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .settings-container {
                    max-width: 800px;
                    margin: 0 auto;
                }

                .settings-header h3 {
                    margin-bottom: 10px;
                    color: #333;
                    font-size: 24px;
                }

                .settings-header p {
                    color: #666;
                    margin-bottom: 30px;
                }

                .settings-section {
                    margin-bottom: 35px;
                }

                .section-header {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 20px;
                    padding-bottom: 10px;
                    border-bottom: 2px solid #f0f0f0;
                }

                .section-icon {
                    font-size: 20px;
                    color: #4b7bec;
                }

                .section-title {
                    font-size: 18px;
                    font-weight: 600;
                    color: #333;
                    margin: 0;
                }

                .setting-item {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 15px 0;
                    border-bottom: 1px solid #f5f5f5;
                }

                .setting-item:last-child {
                    border-bottom: none;
                }

                .setting-info {
                    flex: 1;
                }

                .setting-label {
                    font-size: 15px;
                    font-weight: 500;
                    color: #333;
                    margin-bottom: 3px;
                }

                .setting-description {
                    font-size: 13px;
                    color: #666;
                }

                .toggle-switch {
                    position: relative;
                    width: 50px;
                    height: 26px;
                    flex-shrink: 0;
                }

                .toggle-switch input {
                    opacity: 0;
                    width: 0;
                    height: 0;
                }

                .toggle-slider {
                    position: absolute;
                    cursor: pointer;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background-color: #ccc;
                    transition: 0.3s;
                    border-radius: 26px;
                }

                .toggle-slider:before {
                    position: absolute;
                    content: "";
                    height: 20px;
                    width: 20px;
                    left: 3px;
                    bottom: 3px;
                    background-color: white;
                    transition: 0.3s;
                    border-radius: 50%;
                }

                input:checked + .toggle-slider {
                    background-color: #4b7bec;
                }

                input:checked + .toggle-slider:before {
                    transform: translateX(24px);
                }

                .visibility-options {
                    display: flex;
                    gap: 10px;
                    flex-wrap: wrap;
                }

                .visibility-option {
                    flex: 1;
                    min-width: 120px;
                    padding: 12px 20px;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    text-align: center;
                    cursor: pointer;
                    transition: all 0.2s;
                    background: white;
                }

                .visibility-option:hover {
                    border-color: #4b7bec;
                }

                .visibility-option.active {
                    border-color: #4b7bec;
                    background: #e8f0fe;
                    font-weight: 600;
                }

                .visibility-option input[type="radio"] {
                    display: none;
                }

                .btn-primary {
                    background: #039;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background 0.2s;
                    width: 100%;
                    margin-top: 20px;
                }

                .btn-primary:hover {
                    background: #11306f;
                }

                .btn-primary:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                }

                .alert {
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    font-size: 14px;
                }

                .alert-success {
                    background: #d4edda;
                    color: #155724;
                    border-left: 4px solid #28a745;
                }

                .alert-danger {
                    background: #f8d7da;
                    color: #721c24;
                    border-left: 4px solid #dc3545;
                }

                @media (max-width: 768px) {
                    .visibility-options {
                        flex-direction: column;
                    }

                    .visibility-option {
                        width: 100%;
                    }
                }
            `}</style>

            <div className="settings-wrapper">
                <div className="settings-container">
                    <div className="settings-header">
                        <h3>Account Settings</h3>
                        <p>Manage your privacy and notification preferences</p>
                    </div>

                    {success && (
                        <div className="alert alert-success">
                            ✓ Settings updated successfully!
                        </div>
                    )}

                    {error && (
                        <div className="alert alert-danger">
                            {error}
                        </div>
                    )}

                    <form onSubmit={handleSubmit}>
                        {/* Privacy & Visibility Section */}
                        <div className="settings-section">
                            <div className="section-header">
                                <FaEye className="section-icon" />
                                <h4 className="section-title">Privacy & Visibility</h4>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Profile Visibility</div>
                                    <div className="setting-description">Who can see your profile</div>
                                </div>
                            </div>

                            <div className="visibility-options">
                                <label className={`visibility-option ${settings.profile_visibility === 'public' ? 'active' : ''}`}>
                                    <input
                                        type="radio"
                                        name="profile_visibility"
                                        value="public"
                                        checked={settings.profile_visibility === 'public'}
                                        onChange={() => handleVisibilityChange('public')}
                                    />
                                    Public
                                </label>
                                <label className={`visibility-option ${settings.profile_visibility === 'friends' ? 'active' : ''}`}>
                                    <input
                                        type="radio"
                                        name="profile_visibility"
                                        value="friends"
                                        checked={settings.profile_visibility === 'friends'}
                                        onChange={() => handleVisibilityChange('friends')}
                                    />
                                    Friends Only
                                </label>
                                <label className={`visibility-option ${settings.profile_visibility === 'private' ? 'active' : ''}`}>
                                    <input
                                        type="radio"
                                        name="profile_visibility"
                                        value="private"
                                        checked={settings.profile_visibility === 'private'}
                                        onChange={() => handleVisibilityChange('private')}
                                    />
                                    Private
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Show Email Address</div>
                                    <div className="setting-description">Allow others to see your email</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.show_email}
                                        onChange={() => handleToggle('show_email')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Show Phone Number</div>
                                    <div className="setting-description">Allow others to see your phone</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.show_phone}
                                        onChange={() => handleToggle('show_phone')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Show Activities</div>
                                    <div className="setting-description">Display your activity history</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.show_activities}
                                        onChange={() => handleToggle('show_activities')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        {/* Notifications Section */}
                        <div className="settings-section">
                            <div className="section-header">
                                <FaBell className="section-icon" />
                                <h4 className="section-title">Notifications</h4>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Email Notifications</div>
                                    <div className="setting-description">Receive notifications via email</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.email_notifications}
                                        onChange={() => handleToggle('email_notifications')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Task Reminders</div>
                                    <div className="setting-description">Get notified about pending tasks</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.task_reminders}
                                        onChange={() => handleToggle('task_reminders')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Poll Notifications</div>
                                    <div className="setting-description">Alerts for new polls and surveys</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.poll_notifications}
                                        onChange={() => handleToggle('poll_notifications')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Quiz Notifications</div>
                                    <div className="setting-description">Get notified about new quizzes</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.quiz_notifications}
                                        onChange={() => handleToggle('quiz_notifications')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Achievement Notifications</div>
                                    <div className="setting-description">Celebrate your achievements</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.achievement_notifications}
                                        onChange={() => handleToggle('achievement_notifications')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>

                            <div className="setting-item">
                                <div className="setting-info">
                                    <div className="setting-label">Weekly Digest</div>
                                    <div className="setting-description">Weekly summary of your activities</div>
                                </div>
                                <label className="toggle-switch">
                                    <input
                                        type="checkbox"
                                        checked={settings.weekly_digest}
                                        onChange={() => handleToggle('weekly_digest')}
                                    />
                                    <span className="toggle-slider"></span>
                                </label>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            className="btn-primary"
                            disabled={loading}
                        >
                            {loading ? 'Saving...' : 'Save Settings'}
                        </button>
                    </form>
                </div>
            </div>
        </DashboardLayout>
    );
}
