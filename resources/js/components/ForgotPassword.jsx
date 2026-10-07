"use client";
import { useState } from "react";
import { Link, useNavigate, useLocation } from "react-router-dom";
import { authAPI } from "../services/api";
import { useAuth } from "./App";
import { FaHome } from "react-icons/fa";

export default function ForgotPassword() {
    const [method, setMethod] = useState('email');
    const [formData, setFormData] = useState({
        email: '',
        mobile: '',
        otp: '',
        password: '',
        password_confirmation: ''
    });
    const [message, setMessage] = useState('');
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [otpSent, setOtpSent] = useState(false);
    const [otpVerified, setOtpVerified] = useState(false);
    const [sentOtpHint, setSentOtpHint] = useState('');
    const { authenticateWithToken } = useAuth();
    const navigate = useNavigate();
    const location = useLocation();
    const from = location.state?.from || '/';

    const handleMethodChange = (value) => {
        setMethod(value);
        setFormData({
            email: '',
            mobile: '',
            otp: '',
            password: '',
            password_confirmation: ''
        });
        setErrors({});
        setMessage('');
        setOtpSent(false);
        setOtpVerified(false);
        setSentOtpHint('');
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({ ...prev, [name]: value }));
        if (errors[name]) {
            setErrors((prev) => ({ ...prev, [name]: '' }));
        }
    };

    const sendOTP = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage('');
        setErrors({});

        try {
            const payload = { method };
            if (method === 'email') {
                payload.email = formData.email;
            } else {
                payload.mobile = formData.mobile;
            }

            const { data } = await authAPI.forgotPasswordSendOTP(payload);

            setOtpSent(true);
            setSentOtpHint(data?.otp || '');
            setMessage(data?.message || 'OTP sent successfully.');
        } catch (error) {
            const data = error.response?.data;
            setErrors(data?.errors || {});
            setMessage(data?.message || 'Could not send OTP.');
        } finally {
            setLoading(false);
        }
    };

    const verifyOTP = async (e) => {
        e.preventDefault();
        setLoading(true);
        setMessage('');
        setErrors({});

        try {
            const payload = {
                method,
                otp: formData.otp
            };
            if (method === 'email') {
                payload.email = formData.email;
            } else {
                payload.mobile = formData.mobile;
            }

            const { data } = await authAPI.forgotPasswordVerifyOTP(payload);
            setOtpVerified(true);
            setMessage(data?.message || 'OTP verified. Please set a new password.');
        } catch (error) {
            const data = error.response?.data;
            setOtpVerified(false);
            setErrors(data?.errors || {});
            setMessage(data?.message || 'Could not verify OTP.');
        } finally {
            setLoading(false);
        }
    };

    const resetPassword = async (e) => {
        e.preventDefault();
        if (!otpVerified) {
            setMessage('Please verify OTP first.');
            return;
        }
        setLoading(true);
        setMessage('');
        setErrors({});

        try {
            const payload = {
                method,
                otp: formData.otp,
                password: formData.password,
                password_confirmation: formData.password_confirmation,
            };

            if (method === 'email') {
                payload.email = formData.email;
            } else {
                payload.mobile = formData.mobile;
            }

            const { data } = await authAPI.resetPasswordWithOTP(payload);

            if (data?.token && data?.user) {
                authenticateWithToken(data.user, data.token);
                setMessage('Password reset successful. Signing you in...');
                setTimeout(() => navigate(from, { replace: true }), 1200);
            } else {
                setMessage('Password reset successful. You can sign in now.');
                setTimeout(() => navigate('/login', { replace: true }), 1200);
            }
        } catch (error) {
            const data = error.response?.data;
            setErrors(data?.errors || {});
            setMessage(data?.message || 'Could not reset password.');
        } finally {
            setLoading(false);
        }
    };

    const renderError = (field) => {
        if (!errors[field]) return null;
        const content = Array.isArray(errors[field]) ? errors[field][0] : errors[field];
        return <small className="text-danger d-block mt-1">{content}</small>;
    };

    const otpPlaceholder = 'Enter the OTP you received';

    const alertClass = message && (message.toLowerCase().includes('success') || message.toLowerCase().includes('reset'))
        ? 'alert-success'
        : 'alert-info';

    const toggleButtonStyle = (selected) => ({
        background: selected ? '#282828' : '#ffffff',
        color: selected ? '#ffffff' : '#282828',
        border: '1px solid #282828',
        boxShadow: selected ? '0 8px 16px rgba(13, 71, 213, 0.18)' : 'none',
        minWidth: '180px'
    });

    return (
        <>
            <header id="it-nw-header" className="it-nw-header-area">
                <div className="container-top">
                    <div className="it-nw-header-top-content d-flex justify-content-between">
                        <div className="it-nw-header-cta-social d-flex">
                            <div className="it-nw-header-cta ul-li">
                                <ul>
                                    <li>
                                        <img src="/design/assets/new/loW.svg" alt="" />
                                        <span className="govt">GOVERNMENT OF KERALA</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div className="it-nw-header-login ul-li d-flex">
                            <ul className="shar">
                                <li><a href="#"><span>മലയാളം</span></a></li>
                                <li className="dropdown">
                                    <img src="/design/assets/share.png" alt="" width="20" />
                                    <ul className="dropdown-content">
                                        <li><a href="#"><img src="/design/assets/youtube.png" alt="" /></a></li>
                                        <li><a href="#"><img src="/design/assets/facebook.png" alt="" /></a></li>
                                        <li><a href="#"><img src="/design/assets/twitter.png" alt="" /></a></li>
                                        <li><a href="#"><img src="/design/assets/instagram.png" alt="" /></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </header>

            <div className="wraper1 position-relative">
                <div className="footm">
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                </div>

                <div className="xis-testimonial-shape position-absolute">
                    <img src="/design/assets/dot-map.png" alt="" />
                </div>

                <div className="line_animation">
                    {[...Array(7)].map((_, i) => (
                        <div key={i} className="line_area"></div>
                    ))}
                </div>

                <span className="it-up-service-shape position-absolute deco1">
                    <img src="/design/assets/vect/s-shape1.png" alt="" />
                </span>
                <span className="it-up-service-shape position-absolute deco2">
                    <img src="/design/assets/vect/s-shape2.png" alt="" />
                </span>
                <span className="it-up-service-shape position-absolute deco4">
                    <img src="/design/assets/vect/s-shape4.png" alt="" />
                </span>
                <span className="it-up-service-shape position-absolute deco5">
                    <img src="/design/assets/vect/s-shape5.png" alt="" />
                </span>

                <section id="it-up-contact" className="it-up-contact-section position-relative">
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-5 col-md-6 col-12 col-sm-7 col-xl-5 m-auto">
                                <div className="it-up-form-wrap">
                                    <div className="it-nw-header-logo text-center login-img">
                                        <Link to="/">
                                            <img src="/design/assets/new/enteK.svg" alt="Logo" className="auth-logo" />
                                        </Link>
                                    </div>
                                    <h3 className="text-center mb-2">Forgot Password</h3>
                                    <p className="text-center mb-3">
                                        Choose email or mobile to get your reset OTP. For testing, OTPs are static.
                                    </p>

                                    <div className="d-flex justify-content-center gap-3 mb-3 flex-wrap">
                                        <button
                                            type="button"
                                            className="btn"
                                            style={toggleButtonStyle(method === 'email')}
                                            onClick={() => handleMethodChange('email')}
                                            disabled={loading}
                                        >
                                            Reset via Email
                                        </button>
                                        <button
                                            type="button"
                                            className="btn"
                                            style={toggleButtonStyle(method === 'mobile')}
                                            onClick={() => handleMethodChange('mobile')}
                                            disabled={loading}
                                        >
                                            Reset via Mobile
                                        </button>
                                    </div>

                                    {message && (
                                        <div className={`alert ${alertClass} text-center py-2`}>
                                            {message}
                                        </div>
                                    )}

                                    <form onSubmit={otpSent ? (otpVerified ? resetPassword : verifyOTP) : sendOTP} className="mt-3">
                                        {method === 'email' ? (
                                            <div className="form-group">
                                                <div className="form-control-wrap">
                                                    <input
                                                        type="email"
                                                        className="form-control form-control-lg"
                                                        name="email"
                                                        placeholder="Enter your email"
                                                        value={formData.email}
                                                        onChange={handleChange}
                                                        required
                                                        disabled={loading || otpSent}
                                                    />
                                                </div>
                                                {renderError('email')}
                                            </div>
                                        ) : (
                                            <div className="form-group">
                                                <div className="form-control-wrap">
                                                    <input
                                                        type="tel"
                                                        className="form-control form-control-lg"
                                                        name="mobile"
                                                        placeholder="Enter your mobile number"
                                                        value={formData.mobile}
                                                        onChange={handleChange}
                                                        required
                                                        disabled={loading || otpSent}
                                                    />
                                                </div>
                                                {renderError('mobile')}
                                            </div>
                                        )}

                                        {otpSent && (
                                            <div className="form-group">
                                                <div className="form-control-wrap">
                                                    <input
                                                        type="text"
                                                        className="form-control form-control-lg"
                                                        name="otp"
                                                        placeholder={otpPlaceholder}
                                                        value={formData.otp}
                                                        onChange={handleChange}
                                                        maxLength="6"
                                                        required
                                                        disabled={loading || otpVerified}
                                                    />
                                                </div>
                                                {renderError('otp')}
                                            </div>
                                        )}

                                        {otpVerified && (
                                            <>
                                                <div className="form-group">
                                                    <div className="form-control-wrap">
                                                        <input
                                                            type="password"
                                                            className="form-control form-control-lg"
                                                            name="password"
                                                            placeholder="Enter new password"
                                                            value={formData.password}
                                                            onChange={handleChange}
                                                            required
                                                        />
                                                    </div>
                                                    {renderError('password')}
                                                </div>
                                                <div className="form-group">
                                                    <div className="form-control-wrap">
                                                        <input
                                                            type="password"
                                                            className="form-control form-control-lg"
                                                            name="password_confirmation"
                                                            placeholder="Confirm new password"
                                                            value={formData.password_confirmation}
                                                            onChange={handleChange}
                                                            required
                                                        />
                                                    </div>
                                                    {renderError('password_confirmation')}
                                                </div>
                                            </>
                                        )}

                                        <div className="form-group mt-4">
                                            <button
                                                type="submit"
                                                className="btn signx btn-lg btn-block w-100 text-uppercase"
                                                disabled={loading}>
                                                {loading
                                                    ? 'Please wait...'
                                                    : !otpSent
                                                        ? 'Send OTP'
                                                        : otpVerified
                                                            ? 'Reset Password'
                                                            : 'Verify OTP'}
                                            </button>
                                        </div>
                                    </form>

                                    {otpSent && (
                                        <div className="text-center mt-2">
                                            <button
                                                type="button"
                                                className="btn btn-sm btn-otp-verify my-2 d-flex align-items-center btn-success justify-content-center mx-auto"
                                                onClick={() => handleMethodChange(method)}
                                                disabled={loading}
                                            >
                                                {otpVerified ? 'Change contact' : 'Resend / change contact'}
                                            </button>
                                        </div>
                                    )}

                                    <div className="text-center mt-3">
                                        <Link to="/login">Back to Login</Link>
                                        <Link to="/" className="btn btn-outline-ente-home w-100 mt-2 mb-3 d-flex align-items-center justify-content-center gap-2">
                                            <FaHome /> Back to Home
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="it-up-footer" className="it-up-footer-section position-relative">
                    <div className="it-up-footer-copyright text-center pera-content">
                        <div className="container">
                            <p>© {new Date().getFullYear()} Government of Kerala - Ente Keralam Programme. Developed by C-DIT. All rights reserved.</p>
                        </div>
                    </div>
                </section>
            </div>
        </>
    );
}
