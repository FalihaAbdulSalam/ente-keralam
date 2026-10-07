import { useState } from "react";
import DashboardLayout from "./DashboardLayout";
import { authAPI } from "../services/api";
import { useAuth } from "./App";

export default function UpdateMobile() {
    const { user, setUser } = useAuth();
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');
    const [step, setStep] = useState(1); // 1: enter mobile, 2: verify OTP
    const [formData, setFormData] = useState({
        phone: '',
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
        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const response = await authAPI.sendUpdateMobileOTP({ mobile: formData.phone });
            setSuccess(response.data?.message || 'OTP sent to your mobile number!');
            setStep(2);
        } catch (err) {
            const data = err.response?.data;
            setError(data?.message || 'Failed to send OTP');
            if (data?.errors?.mobile) {
                setError(data.errors.mobile[0]);
            }
        } finally {
            setLoading(false);
        }
    };

    const handleVerifyOTP = async (e) => {
        e.preventDefault();
        setLoading(true);
        setSuccess('');
        setError('');

        try {
            const response = await authAPI.updateMobileWithOTP({
                mobile: formData.phone,
                otp: formData.otp,
            });
            setSuccess(response.data?.message || 'Mobile number updated successfully!');
            
            // Update user in context
            if (setUser && response.data?.user) {
                setUser(response.data.user);
            }
            
            // Reset form after 2 seconds
            setTimeout(() => {
                setStep(1);
                setFormData({ phone: '', otp: '' });
            }, 2000);
        } catch (err) {
            const data = err.response?.data;
            setError(data?.message || 'Invalid OTP');
            if (data?.errors?.mobile) {
                setError(data.errors.mobile[0]);
            }
        } finally {
            setLoading(false);
        }
    };

    return (
        <DashboardLayout>
            <style>{`
                .update-mobile-wrapper {
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
                    .update-mobile-wrapper {
                        padding: 20px;
                    }
                    
                    .update-card {
                        padding: 0;
                    }
                }
            `}</style>

            <div className="update-mobile-wrapper">
                <div className="update-card">
                <h3>Update Mobile Number</h3>

                {/* Current Mobile */}
                <div className="current-info">
                    <label>Current Mobile Number</label>
                    <div className="value">{user?.phone || 'Not set'}</div>
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
                            <label>New Mobile Number *</label>
                            <input
                                type="tel"
                                name="phone"
                                className="form-control"
                                value={formData.phone}
                                onChange={handleChange}
                                placeholder="Enter 10-digit mobile number"
                                maxLength="10"
                                pattern="[0-9]{10}"
                                required
                            />
                            <p className="info-text">
                                We'll send an OTP to this mobile number for verification.
                            </p>
                        </div>

                        <button type="submit" className="btn-primary" disabled={loading}>
                            {loading ? 'Sending OTP...' : 'Send OTP'}
                        </button>
                    </form>
                ) : (
                    <form onSubmit={handleVerifyOTP}>
                        <div className="form-group">
                            <label>Enter OTP *</label>
                            <input
                                type="text"
                                name="otp"
                                className="form-control"
                                value={formData.otp}
                                onChange={handleChange}
                                placeholder="Enter 6-digit OTP"
                                maxLength="6"
                                pattern="[0-9]{6}"
                                required
                            />
                            <p className="info-text">
                                Enter the OTP sent to {formData.phone}
                            </p>
                        </div>

                        <button type="submit" className="btn-primary" disabled={loading}>
                            {loading ? 'Verifying...' : 'Verify & Update Mobile'}
                        </button>
                        
                        <button 
                            type="button" 
                            className="btn-secondary" 
                            onClick={() => setStep(1)}
                            disabled={loading}
                        >
                            Change Mobile Number
                        </button>
                    </form>
                )}
                </div>
            </div>
        </DashboardLayout>
    );
}
