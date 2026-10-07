import { useState } from "react";
import DashboardLayout from "./DashboardLayout";
import { authAPI } from "../services/api";
import { useAuth } from "./App";

export default function UpdateEmail() {
    const { user, setUser } = useAuth();
    const isPlaceholderEmail = user?.email && user.email.endsWith('@entekeralam.kerala.gov.in');
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');
    const [step, setStep] = useState(1); // 1: enter email, 2: verify OTP
    const [formData, setFormData] = useState({
        email: '',
        otp: ''
    });

    const handleChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handleSendOTP = async (e) => {
        e.preventDefault();
        if (user?.provider) {
            setError('Email updates are disabled for social accounts.');
            return;
        }
        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const response = await authAPI.sendUpdateEmailOTP({ email: formData.email });
            setSuccess(response.data?.message || 'Verification code sent to your email!');
            setStep(2);
        } catch (err) {
            const data = err.response?.data;
            setError(data?.message || 'Failed to send verification code');
            if (data?.errors?.email) {
                setError(data.errors.email[0]);
            }
        } finally {
            setLoading(false);
        }
    };

    const handleVerifyOTP = async (e) => {
        e.preventDefault();
        if (user?.provider) {
            setError('Email updates are disabled for social accounts.');
            return;
        }
        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const response = await authAPI.updateEmailWithOTP(formData);
            setSuccess(response.data?.message || 'Email updated successfully!');
            
            // Update user in context
            if (setUser && response.data?.user) {
                setUser(response.data.user);
            }
            
            // Reset form after 2 seconds
            setTimeout(() => {
                setStep(1);
                setFormData({ email: '', otp: '' });
            }, 2000);
        } catch (err) {
            const data = err.response?.data;
            setError(data?.message || 'Invalid verification code');
            if (data?.errors?.email) {
                setError(data.errors.email[0]);
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <DashboardLayout>
            <style>{`
                .update-email-wrapper {
                    background: rgba(255, 255, 255, 0.9);
                    border-radius: 15px;
                    padding: 40px;
                    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
                    width: 100%;
                    max-width: 1200px;
                    margin: 0 auto;
                }

                .update-card {
                    max-width: 500px;
                    margin: 0 auto;
                }

                .update-card h3 {
                    margin-bottom: 20px;
                    color: #333;
                }

                .current-info {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 25px;
                    border-left: 4px solid #4b7bec;
                }

                .current-info label {
                    display: block;
                    font-size: 12px;
                    color: #666;
                    margin-bottom: 5px;
                }

                .current-info .value {
                    font-size: 16px;
                    font-weight: 600;
                    color: #333;
                }

                .form-group {
                    margin-bottom: 20px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                    color: #555;
                    font-weight: 500;
                }

                .form-control {
                    width: 100%;
                    padding: 12px 15px;
                    border: 1px solid #ddd;
                    border-radius: 6px;
                    font-size: 14px;
                }

                .form-control:focus {
                    outline: none;
                    border-color: #4b7bec;
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
                }

                .btn-primary:hover {
                    background: #11306f;
                }

                .btn-primary:disabled {
                    background: #95a5a6;
                    cursor: not-allowed;
                }

                .btn-secondary {
                    background: #6c757d;
                    color: white;
                    border: none;
                    padding: 12px 30px;
                    border-radius: 6px;
                    cursor: pointer;
                    font-size: 16px;
                    transition: background 0.2s;
                    width: 100%;
                    margin-top: 10px;
                }

                .btn-secondary:hover {
                    background: #5a6268;
                }

                .alert {
                    padding: 12px 15px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                }

                .alert-success {
                    background: #d4edda;
                    color: #155724;
                    border: 1px solid #c3e6cb;
                }

                .alert-danger {
                    background: #f8d7da;
                    color: #721c24;
                    border: 1px solid #f5c6cb;
                }

                .info-text {
                    font-size: 13px;
                    color: #666;
                    margin-top: 10px;
                    line-height: 1.5;
                }

                .step-indicator {
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    margin-bottom: 30px;
                    gap: 0;
                }

                .step {
                    width: 40px;
                    height: 40px;
                    border-radius: 50%;
                    background: #e0e0e0;
                    color: #666;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-weight: 600;
                    position: relative;
                    z-index: 1;
                }

                .step.active {
                    background: #4b7bec;
                    color: white;
                }

                .step.completed {
                    background: #27ae60;
                    color: white;
                }

                .step:not(:last-child)::after {
                    content: '';
                    position: absolute;
                    width: 80px;
                    height: 2px;
                    background: #e0e0e0;
                    left: calc(100% + 0px);
                    top: 50%;
                    transform: translateY(-50%);
                    z-index: 0;
                }

                .step:not(:last-child) {
                    margin-right: 80px;
                }

                @media (max-width: 768px) {
                    .update-email-wrapper {
                        padding: 20px;
                    }
                    
                    .update-card {
                        padding: 0;
                    }
                }
            `}</style>

            <div className="update-email-wrapper">
                <div className="update-card">
                <h3>Update Email Address</h3>

                {user?.provider && (
                    <div className="alert alert-info">
                        Email changes are disabled for social sign-ins. Please continue using your provider email.
                    </div>
                )}

                {/* Current Email */}
                <div className="current-info">
                    <label>Current Email</label>
                    <div className="value">
                        {isPlaceholderEmail ? (
                            "Please update your email address so we can send notifications to you."
                        ) : (
                            (user?.email || 'Not set')
                        )}
                    </div>
                </div>

                {/* Step Indicator */}
                <div className="step-indicator">
                    <div className={`step ${step >= 1 ? 'active' : ''}`}>1</div>
                    <div className={`step ${step >= 2 ? 'active' : ''}`}>2</div>
                </div>

                {success && <div className="alert alert-success">{success}</div>}
                {error && <div className="alert alert-danger">{error}</div>}

                {step === 1 ? (
                    <form onSubmit={handleSendOTP}>
                        <div className="form-group">
                            <label>New Email Address *</label>
                            <input
                                type="email"
                                name="email"
                                className="form-control"
                                value={formData.email}
                                onChange={handleChange}
                                placeholder="Enter your new email"
                                required
                            />
                            <p className="info-text">
                                We'll send a verification code to this email address.
                            </p>
                        </div>

                        <button type="submit" className="btn-primary" disabled={loading || user?.provider}>
                            {loading ? 'Sending...' : 'Send Verification Code'}
                        </button>
                    </form>
                ) : (
                    <form onSubmit={handleVerifyOTP}>
                        <div className="form-group">
                            <label>Verification Code *</label>
                            <input
                                type="text"
                                name="otp"
                                className="form-control"
                                value={formData.otp}
                                onChange={handleChange}
                                placeholder="Enter 6-digit code"
                                maxLength="6"
                                required
                            />
                            <p className="info-text">
                                Enter the verification code sent to {formData.email}
                            </p>
                        </div>

                        <button type="submit" className="btn-primary" disabled={loading || user?.provider}>
                            {loading ? 'Verifying...' : 'Verify & Update Email'}
                        </button>
                        
                        <button 
                            type="button" 
                            className="btn-secondary" 
                            onClick={() => setStep(1)}
                            disabled={loading || user?.provider}
                        >
                            Change Email Address
                        </button>
                    </form>
                )}
                </div>
            </div>
        </DashboardLayout>
    );
}
