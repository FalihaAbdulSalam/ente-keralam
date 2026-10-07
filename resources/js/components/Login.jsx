"use client";
import { useState, useEffect, useRef } from "react";
import { Link, useNavigate, useLocation } from "react-router-dom";
import { FaChevronUp, FaBars, FaTimesCircle, FaSearch, FaArrowRight, FaHome, FaPaperPlane, FaSignInAlt, FaCheckCircle, FaUserPlus } from "react-icons/fa";
import { ToastContainer, toast } from 'react-toastify';
import "react-toastify/dist/ReactToastify.css";
import { useAuth } from './App';

export default function LoginPage() {
    const [formData, setFormData] = useState({
        mobile: '',
        email: '',
        password: '',
        user_login: ''
    });
    const [submitLoading, setSubmitLoading] = useState(false);
    const [showOTPInput, setShowOTPInput] = useState(false);
    const [generatedOTP, setGeneratedOTP] = useState('');
    const [otpInput, setOTPInput] = useState('');
    const navigate = useNavigate();
    const location = useLocation();
    const { login, authenticateWithToken, isAuthenticated, loading } = useAuth();
    const hasHandledSocialLogin = useRef(false);

    // Get the redirect URL from query param or location state.
    // Security: only allow same-origin relative paths.
    const urlParams = new URLSearchParams(location.search);
    const redirectParam = urlParams.get('redirect');
    const safeRedirect = (value) => {
        if (!value || typeof value !== 'string') return null;
        // Allow only site-relative paths like /competition-details/slug
        if (value.startsWith('/') && !value.startsWith('//')) return value;
        return null;
    };
    const requestRedirect = safeRedirect(redirectParam) || safeRedirect(location.state?.from);
    const from = requestRedirect || '/';

    // Redirect if already authenticated
    useEffect(() => {
        if (!loading && isAuthenticated) {
            navigate(from, { replace: true });
        }
    }, [isAuthenticated, loading, navigate, from]);

    // Handle social login callback
    useEffect(() => {
        const urlParams = new URLSearchParams(location.search);
        const socialLogin = urlParams.get('social_login');
        const token = urlParams.get('token');
        const userParam = urlParams.get('user');
        const errorMessage = urlParams.get('message');

        if (hasHandledSocialLogin.current) return;

        if (socialLogin === 'success' && token && userParam) {
            try {
                // user param is base64 + URL encoded by backend
                const decodedUserParam = decodeURIComponent(userParam);
                const normalizedUserParam = decodedUserParam.replace(/ /g, '+'); // handle + decoded as space
                const user = JSON.parse(atob(normalizedUserParam));
                const loginResult = authenticateWithToken(user, token);
                
                if (loginResult.success) {
                    // If required profile data is missing (e.g., no mobile), push user to update mobile first
                    const needsMobile = !user?.phone;
                    toast.success(needsMobile ? 'Login successful! Please add your mobile number.' : 'Social login successful! Redirecting...');
                    // Clean URL and redirect
                    window.history.replaceState({}, document.title, '/login');
                    setTimeout(() => {
                        if (needsMobile) {
                            navigate('/update-mobile', { replace: true });
                        } else {
                            navigate(from, { replace: true });
                        }
                    }, 1500);
                }
                hasHandledSocialLogin.current = true;
            } catch (error) {
                toast.error('Error processing social login');
                hasHandledSocialLogin.current = true;
            }
        } else if (socialLogin === 'error' && errorMessage) {
            toast.error(decodeURIComponent(errorMessage));
            hasHandledSocialLogin.current = true;
        }
    }, [location, authenticateWithToken, navigate]);

    const handleInputChange = (e) => {
        setFormData({
            ...formData,
            [e.target.name]: e.target.value
        });
    };

    const handleOTPLogin = async (e) => {
        e.preventDefault();
        setSubmitLoading(true);

        try {
            const response = await fetch('/api/auth/send-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    mobile: formData.mobile
                })
            });

            const data = await response.json();

            if (response.ok) {
                setGeneratedOTP(data.otp);
                if (data.otp) {
                    setOTPInput(data.otp);
                }
                setShowOTPInput(true);
                toast.success(`OTP sent successfully to ${formData.mobile}! ${data.otp ? `(OTP: ${data.otp})` : 'Enter the OTP below.'}`);
            } else {
                toast.error(data.message || 'Failed to send OTP');
            }
        } catch (error) {
            toast.error('Network error occurred');
        }
        setSubmitLoading(false);
    };

    const handleOTPVerification = async (e) => {
        e.preventDefault();
        setSubmitLoading(true);

        try {
            const response = await fetch('/api/auth/verify-otp', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    mobile: formData.mobile,
                    otp: otpInput
                }),
            });

            const data = await response.json();

            if (data.success) {
                // Use the AuthContext authenticateWithToken to set session properly
                const loginResult = authenticateWithToken(data.user, data.token);

                if (loginResult.success) {
                    toast.success('OTP verified successfully! Logging you in...');
                    setTimeout(() => {
                        navigate(from, { replace: true });
                    }, 1500);
                } else {
                    toast.error('Authentication failed after OTP verification');
                }
            } else {
                toast.error(data.message || 'Invalid OTP. Please try again.');
            }
        } catch (error) {
            toast.error('Network error occurred during OTP verification');
        }
        setSubmitLoading(false);
    };

    const handlePasswordLogin = async (e) => {
        e.preventDefault();
        setSubmitLoading(true);

        try {
            const result = await login({
                user_login: formData.user_login, // Send user_login as expected by backend
                password: formData.password
            });
            
            if (result.success) {
                toast.success('Login successful! Redirecting...');
                setTimeout(() => {
                    navigate(from, { replace: true });
                }, 1500);
            } else {
                toast.error(result.message || 'Login failed');
            }
        } catch (error) {
            toast.error('Network error occurred');
        }
        setSubmitLoading(false);
    };

    const handleSocialLogin = (provider) => {
        window.location.href = `/api/auth/${provider}`;
    };

    return (
        <>
            <ToastContainer
                position="top-right"
                autoClose={3000}
                hideProgressBar={false}
                newestOnTop={false}
                closeOnClick
                rtl={false}
                pauseOnFocusLoss
                draggable
                pauseOnHover
            />
            <header id="it-nw-header" className="it-nw-header-area">
                <div className="container-top">
                    {/* Top Bar */}
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
                                {/* <li><a href="#">Skip to Main content</a></li> */}
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
                {/* Background foot images */}
                <div className="footm">
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                </div>

                <div className="xis-testimonial-shape position-absolute">
                    <img src="/design/assets/dot-map.png" alt="" />
                </div>

                {/* Line animation */}
                <div className="line_animation">
                    {[...Array(7)].map((_, i) => (
                        <div key={i} className="line_area"></div>
                    ))}
                </div>

                {/* Decorative shapes */}
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

                {/* Contact/Login Section */}
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
                                    <h3 className="text-center mb-4">SIGN IN</h3>

                                    <div className="register-cta-section mb-4" style={{
                                        background: '#f8f9fa',
                                        padding: '20px',
                                        borderRadius: '6px',
                                        textAlign: 'center',
                                        transition: 'all 0.3s ease'
                                    }}>
                                        <p style={{
                                            margin: '0 0 12px 0',
                                            color: '#333',
                                            fontSize: '14px',
                                            fontWeight: '600'
                                        }}>
                                            Don't have an account?
                                        </p>
                                        <button 
                                            type="button"
                                            onClick={() => {
                                                const qs = requestRedirect ? `?redirect=${encodeURIComponent(requestRedirect)}` : '';
                                                navigate(`/register${qs}`);
                                            }}
                                            className="btn w-100 text-uppercase" 
                                            style={{
                                                color: '#039',
                                                border: '2px solid #039',
                                                backgroundColor: 'transparent',
                                                fontWeight: '700',
                                                padding: '12px 20px',
                                                fontSize: '15px',
                                                borderRadius: '4px',
                                                transition: 'all 0.3s ease',
                                                textDecoration: 'none',
                                                letterSpacing: '0.5px',
                                                cursor: 'pointer',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                gap: '10px'
                                            }}
                                            onMouseEnter={(e) => {
                                                e.target.style.backgroundColor = '#039';
                                                e.target.style.color = 'white';
                                            }}
                                            onMouseLeave={(e) => {
                                                e.target.style.backgroundColor = 'transparent';
                                                e.target.style.color = '#039';
                                            }}>
                                            <FaUserPlus style={{ fontSize: '24px' }} />
                                            Register Here
                                        </button>
                                        <p style={{
                                            margin: '10px 0 0 0',
                                            color: '#666',
                                            fontSize: '13px'
                                        }}>
                                            Join Kerala's citizen engagement platform
                                        </p>
                                    </div>

                                    {/* OTP Login */}
                                    <div className="social">
                                        <div className="fb mb-2" onClick={() => handleSocialLogin('facebook')} style={{ cursor: 'pointer' }}>
                                            <img src="/design/assets/facebook.svg" width="28" alt="facebook" /> Facebook
                                        </div>
                                        {/* <div className="gle mr-3" onClick={() => handleSocialLogin('google')} style={{ cursor: 'pointer' }}>
                                            <img src="/design/assets/google.svg" width="28" alt="google" /> Google
                                        </div> */}
                                    </div>

                                    <p className="mt-3 or">OR</p>

                                    {/* OTP Login */}
                                    {!showOTPInput ? (
                                        <form onSubmit={handleOTPLogin} className="mt-3">
                                            <div className="form-group">
                                                <div className="form-control-wrap">
                                                    <input
                                                        type="tel"
                                                        className="form-control form-control-lg"
                                                        id="mobile"
                                                        name="mobile"
                                                        placeholder="Mobile number"
                                                        value={formData.mobile}
                                                        onChange={handleInputChange}
                                                        required />
                                                </div>
                                            </div>
                                            <div className="form-group mt-4">
                                                <button
                                                    type="submit"
                                                    className="btn signx btn-lg btn-block w-100 text-uppercase"
                                                    disabled={submitLoading || !formData.mobile}
                                                    style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '8px' }}>
                                                    {submitLoading ? 'Sending...' : <><FaPaperPlane /> <span>Send OTP</span></>}
                                                </button>
                                            </div>
                                        </form>
                                    ) : (
                                        <form onSubmit={handleOTPVerification} className="mt-3">
                                            <div className="form-group">
                                                <div className="form-control-wrap">
                                                    <input
                                                        type="tel"
                                                        className="form-control form-control-lg"
                                                        id="mobile"
                                                        name="mobile"
                                                        placeholder="Mobile number"
                                                        value={formData.mobile}
                                                        disabled
                                                        style={{ backgroundColor: '#f8f9fa' }} />
                                                </div>
                                            </div>
                                            <div className="form-group">
                                                <div className="form-control-wrap">
                                                    <input
                                                        type="text"
                                                        className="form-control form-control-lg"
                                                        id="otp"
                                                        name="otp"
                                                        placeholder="Enter OTP"
                                                        value={otpInput}
                                                        onChange={(e) => setOTPInput(e.target.value)}
                                                        maxLength="6"
                                                        required />
                                                </div>
                                            </div>
                                            <div className="form-group mt-4">
                                                <button
                                                    type="submit"
                                                    className="btn signx btn-lg btn-block w-100"
                                                    disabled={submitLoading || !otpInput}
                                                    style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '8px' }}
                                                >
                                                    {submitLoading ? 'Verifying...' : <><FaCheckCircle /> <span>Verify OTP & Sign In</span></>}
                                                </button>
                                            </div>
                                            <div className="text-center mt-2">
                                                <button 
                                                    type="button" 
                                                    className="btn btn-sm btn-otp-verify my-2 d-flex align-items-center btn-success justify-content-center mx-auto"
                                                    onClick={() => {
                                                        setShowOTPInput(false);
                                                        setOTPInput('');
                                                        setMessage('');
                                                    }}
                                                >
                                                    {/* ← */}
                                                    Back to mobile number
                                                </button>
                                            </div>
                                        </form>
                                    )}

                                    <div className="pt-4 mx-auto lyn"></div>

                                    <p className="or">OR</p>

                                    {/* Username + Password Login */}
                                    <form onSubmit={handlePasswordLogin} className="mt-3">
                                        <div className="form-group">
                                            <div className="form-control-wrap">
                                                <input
                                                    type="text"
                                                    className="form-control form-control-lg"
                                                    id="user_login"
                                                    name="user_login"
                                                    placeholder="Mobile number or email address"
                                                    value={formData.user_login}
                                                    onChange={handleInputChange}
                                                    required />
                                            </div>
                                        </div>
                                        <div className="form-group">
                                            <div className="form-control-wrap">
                                                <input
                                                    type="password"
                                                    className="form-control form-control-lg"
                                                    id="password"
                                                    name="password"
                                                    placeholder="Enter your password"
                                                    value={formData.password}
                                                    onChange={handleInputChange}
                                                    required />
                                            </div>
                                            <Link to="/forgot-password" className="forgot">
                                                Forgot Password?
                                            </Link>
                                        </div>

                                        {/* Captcha (optional / hidden by default) */}
                                        <div className="form-group d-none">
                                            <div className="captcha-control-wrap wrapx form-control-wrap">
                                                <input
                                                    type="text"
                                                    className="form-control mt-1 form-control-lg"
                                                    id="captcha"
                                                    name="captcha"
                                                    placeholder="Enter CAPTCHA"
                                                    minLength="6"
                                                    maxLength="6" />
                                            </div>
                                        </div>

                                        <div className="form-group mt-4">
                                            <button
                                                type="submit"
                                                className="btn signx btn-lg btn-block w-100"
                                                disabled={submitLoading}
                                                style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: '8px' }}
                                            >
                                                {submitLoading ? 'Signing in...' : <><FaSignInAlt /> <span>SIGN IN</span></>}
                                            </button>
                                        </div>
                                    </form>

                                    {/* Register link */}
                                    <div className="text-center">
                                        <Link to="/" className="btn btn-outline-ente-home w-100 mt-4 mb-5 d-flex align-items-center justify-content-center gap-2">
                                            <FaHome /> Back to Home
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Footer Section */}
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