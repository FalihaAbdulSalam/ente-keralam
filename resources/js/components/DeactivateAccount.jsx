import { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import DashboardLayout from './DashboardLayout';
import { useAuth } from './App';
import dashboardAPI from '../services/dashboardAPI';
import { FaExclamationTriangle } from 'react-icons/fa';

export default function DeactivateAccount() {
    const navigate = useNavigate();
    const { logout } = useAuth();
    const [formData, setFormData] = useState({
        reason: '',
        feedback: ''
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [showConfirmModal, setShowConfirmModal] = useState(false);

    const deactivationReasons = [
        'Taking a break from the platform',
        'Privacy concerns',
        'Not useful anymore',
        'Too many notifications',
        'Account security concerns',
        'Other'
    ];

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (!formData.reason) {
            setError('Please select a reason for deactivation');
            return;
        }

        setShowConfirmModal(true);
    };

    const confirmDeactivation = async () => {
        setLoading(true);
        setError('');

        try {
            await dashboardAPI.deactivateAccount(formData);
            
            // Logout and redirect to home
            logout();
            navigate('/');
        } catch (err) {
            setError(err.response?.data?.message || 'Failed to deactivate account');
            setShowConfirmModal(false);
        } finally {
            setLoading(false);
        }
    };

    return (
        <DashboardLayout>
            <style>{`
                .deactivate-wrapper {
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .deactivate-container {
                    background: white;
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    max-width: 600px;
                    margin: 0 auto;
                }

                .deactivate-header {
                    margin-bottom: 30px;
                }

                .deactivate-header h2 {
                    font-size: 28px;
                    color: #333;
                    margin-bottom: 10px;
                    font-weight: 600;
                }

                .deactivate-header p {
                    color: #666;
                    font-size: 15px;
                    margin: 0;
                }

                .warning-box {
                    background: #fff3cd;
                    border-left: 4px solid #ffc107;
                    padding: 20px;
                    border-radius: 8px;
                    margin-bottom: 30px;
                    display: flex;
                    gap: 15px;
                    align-items: start;
                }

                .warning-icon {
                    color: #ffc107;
                    font-size: 24px;
                    flex-shrink: 0;
                }

                .warning-content h4 {
                    font-size: 16px;
                    color: #856404;
                    margin-bottom: 10px;
                    font-weight: 600;
                }

                .warning-content ul {
                    margin: 0;
                    padding-left: 20px;
                }

                .warning-content li {
                    font-size: 14px;
                    color: #856404;
                    margin-bottom: 5px;
                }

                .form-group {
                    margin-bottom: 25px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 10px;
                    color: #333;
                    font-weight: 500;
                    font-size: 14px;
                }

                .reason-options {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;
                }

                .reason-option {
                    display: flex;
                    align-items: center;
                    padding: 12px 15px;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .reason-option:hover {
                    border-color: #4b7bec;
                    background: #f8f9fa;
                }

                .reason-option input[type="radio"] {
                    margin-right: 12px;
                    cursor: pointer;
                }

                .reason-option.selected {
                    border-color: #4b7bec;
                    background: #e8f0fe;
                }

                .form-group textarea {
                    width: 100%;
                    padding: 12px 15px;
                    border: 2px solid #e0e0e0;
                    border-radius: 8px;
                    font-size: 15px;
                    font-family: inherit;
                    min-height: 120px;
                    resize: vertical;
                    transition: all 0.3s ease;
                }

                .form-group textarea:focus {
                    outline: none;
                    border-color: #4b7bec;
                    box-shadow: 0 0 0 3px rgba(75, 123, 236, 0.1);
                }

                .btn-danger {
                    width: 100%;
                    padding: 14px;
                    background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 16px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .btn-danger:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 5px 20px rgba(231, 76, 60, 0.4);
                }

                .btn-danger:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                    transform: none;
                }

                .alert-danger {
                    background: #f8d7da;
                    color: #721c24;
                    border-left: 4px solid #dc3545;
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    font-size: 14px;
                }

                .modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.6);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 1000;
                }

                .modal-content {
                    background: white;
                    border-radius: 15px;
                    padding: 40px;
                    max-width: 500px;
                    width: 90%;
                    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                }

                .modal-header {
                    text-align: center;
                    margin-bottom: 25px;
                }

                .modal-icon {
                    font-size: 64px;
                    color: #e74c3c;
                    margin-bottom: 20px;
                }

                .modal-header h3 {
                    font-size: 24px;
                    color: #333;
                    margin-bottom: 10px;
                    font-weight: 600;
                }

                .modal-header p {
                    color: #666;
                    font-size: 15px;
                    margin: 0;
                }

                .modal-buttons {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 15px;
                    margin-top: 25px;
                }

                .btn-cancel {
                    padding: 12px;
                    background: #6c757d;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .btn-cancel:hover {
                    background: #5a6268;
                }

                .btn-confirm {
                    padding: 12px;
                    background: #e74c3c;
                    color: white;
                    border: none;
                    border-radius: 8px;
                    font-size: 15px;
                    font-weight: 600;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                .btn-confirm:hover {
                    background: #c0392b;
                }

                .btn-confirm:disabled {
                    opacity: 0.6;
                    cursor: not-allowed;
                }

                @media (max-width: 768px) {
                    .deactivate-container {
                        padding: 25px;
                    }

                    .deactivate-header h2 {
                        font-size: 24px;
                    }

                    .modal-content {
                        padding: 30px 20px;
                    }

                    .modal-buttons {
                        grid-template-columns: 1fr;
                    }
                }
            `}</style>

            <div className="deactivate-wrapper">
                <div className="deactivate-container">
                    <div className="deactivate-header">
                        <h2>Deactivate Account</h2>
                        <p>We're sorry to see you go</p>
                    </div>

                    <div className="warning-box">
                        <div className="warning-icon">
                            <FaExclamationTriangle />
                        </div>
                        <div className="warning-content">
                            <h4>Important Information</h4>
                            <ul>
                                <li>Your account will be deactivated immediately</li>
                                <li>You will lose access to all your data and activities</li>
                                <li>Your points and achievements will be lost</li>
                                <li>This action cannot be easily undone</li>
                            </ul>
                        </div>
                    </div>

                    {error && (
                        <div className="alert-danger">
                            {error}
                        </div>
                    )}

                    <form onSubmit={handleSubmit}>
                        <div className="form-group">
                            <label>Reason for Deactivation *</label>
                            <div className="reason-options">
                                {deactivationReasons.map((reason, index) => (
                                    <label 
                                        key={index} 
                                        className={`reason-option ${formData.reason === reason ? 'selected' : ''}`}
                                    >
                                        <input
                                            type="radio"
                                            name="reason"
                                            value={reason}
                                            checked={formData.reason === reason}
                                            onChange={handleChange}
                                        />
                                        {reason}
                                    </label>
                                ))}
                            </div>
                        </div>

                        <div className="form-group">
                            <label htmlFor="feedback">Additional Feedback (Optional)</label>
                            <textarea
                                id="feedback"
                                name="feedback"
                                value={formData.feedback}
                                onChange={handleChange}
                                placeholder="Help us improve by sharing your thoughts..."
                            />
                        </div>

                        <button 
                            type="submit" 
                            className="btn-danger"
                            disabled={loading}
                        >
                            Deactivate My Account
                        </button>
                    </form>
                </div>
            </div>

            {showConfirmModal && (
                <div className="modal-overlay">
                    <div className="modal-content">
                        <div className="modal-header">
                            <div className="modal-icon">
                                <FaExclamationTriangle />
                            </div>
                            <h3>Are you absolutely sure?</h3>
                            <p>This action will deactivate your account and you will be logged out immediately.</p>
                        </div>

                        <div className="modal-buttons">
                            <button 
                                className="btn-cancel"
                                onClick={() => setShowConfirmModal(false)}
                                disabled={loading}
                            >
                                Cancel
                            </button>
                            <button 
                                className="btn-confirm"
                                onClick={confirmDeactivation}
                                disabled={loading}
                            >
                                {loading ? 'Deactivating...' : 'Yes, Deactivate'}
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </DashboardLayout>
    );
}
