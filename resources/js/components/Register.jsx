"use client";
import { useState, useEffect, useRef } from "react";
import { Link, useNavigate, useLocation } from "react-router-dom";
import { useAuth } from './App';
import { FaHome } from "react-icons/fa";
import { authAPI } from "../services/api";
import { ToastContainer, toast } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import DateOfBirthPicker from "./DateOfBirthPicker";

export default function RegisterPage() {
    const [formData, setFormData] = useState({
        name: '',
        role: 'public', // Default value set to public
        dob: '',
        gender: '',
        email: '',
        mobile: '',
        password: '',
        password_confirmation: '',
        referral_code: ''
    });
    const [errors, setErrors] = useState({});
    const [loading, setLoading] = useState(false);
    const [showOTP, setShowOTP] = useState(false);
    const [otpData, setOTPData] = useState({ mobile_otp: '' });
    const [showMobileOTP, setShowMobileOTP] = useState(false);
    const [otpValues, setOTPValues] = useState({ mobile_otp_input: '' });
    const [otpVerified, setOTPVerified] = useState({ mobile: false });
    const [isVerifying, setIsVerifying] = useState({ mobile: false });
    const [isGeneratingOTP, setIsGeneratingOTP] = useState({ mobile: false });
    const [message, setMessage] = useState('');
    const [mobileOtpAttempts, setMobileOtpAttempts] = useState(0);
    const hasHandledSocialLogin = useRef(false);
    const handleDobChange = (dobStr) => {
        setFormData(prev => ({ ...prev, dob: dobStr }));
        setErrors(prev => ({ ...prev, dob: '' }));
    };
    
    const { register, authenticateWithToken } = useAuth();
    // Render helper: ignore empty-string/empty-array entries so we don't show "dob" with a blank message.
    const getRenderableErrors = (errs) => {
        if (!errs || typeof errs !== 'object') return {};
        const out = {};
        Object.entries(errs).forEach(([key, val]) => {
            if (key === 'general') return;
            if (val == null) return;
            if (Array.isArray(val)) {
                const cleaned = val.map(v => String(v || '').trim()).filter(Boolean);
                if (cleaned.length) out[key] = cleaned;
                return;
            }
            const trimmed = String(val).trim();
            if (trimmed) out[key] = trimmed;
        });
        return out;
    };
    const navigate = useNavigate();
    const location = useLocation();

    // Get the redirect URL from query param or location state.
    // Security: only allow same-origin relative paths.
    const safeRedirect = (value) => {
        if (!value || typeof value !== 'string') return null;
        if (value.startsWith('/') && !value.startsWith('//')) return value;
        return null;
    };
    const urlParamsForRedirect = new URLSearchParams(location.search);
    const redirectParam = urlParamsForRedirect.get('redirect');
    const from = safeRedirect(redirectParam) || location.state?.from || '/';

    // Auto-fill referral code from URL parameter if present
    useEffect(() => {
        const urlParams = new URLSearchParams(location.search);
        const refCode = urlParams.get('ref') || urlParams.get('referral_code');
        if (refCode) {
            setFormData(prev => ({
                ...prev,
                referral_code: refCode
            }));
        }
    }, [location.search]);

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData(prev => ({
            ...prev,
            [name]: value
        }));
        // Clear error when user starts typing
        if (errors[name]) {
            setErrors(prev => ({
                ...prev,
                [name]: ''
            }));
        }
    };

    // Handle social login callback (same params as login page)
    useEffect(() => {
        const urlParams = new URLSearchParams(location.search);
        const socialLogin = urlParams.get('social_login');
        const token = urlParams.get('token');
        const userParam = urlParams.get('user');
        const errorMessage = urlParams.get('message');

        if (hasHandledSocialLogin.current) return;

        if (socialLogin === 'success' && token && userParam) {
            try {
                const decodedUserParam = decodeURIComponent(userParam);
                const normalizedUserParam = decodedUserParam.replace(/ /g, '+');
                const user = JSON.parse(atob(normalizedUserParam));
                const loginResult = authenticateWithToken
                    ? authenticateWithToken(user, token)
                    : null;

                if (loginResult && loginResult.success) {
                    const needsMobile = !user?.phone;
                    const message = needsMobile ? 'Login successful! Please add your mobile number.' : 'Social login successful! Redirecting...';
                    setMessage(message);
                    toast.success(message, {
                        position: 'top-right',
                        autoClose: 2000
                    });
                    window.history.replaceState({}, document.title, '/register');
                    setTimeout(() => {
                        if (needsMobile) {
                            navigate('/update-mobile', { replace: true });
                        } else {
                            navigate(from, { replace: true });
                        }
                    }, 2000);
                }
                hasHandledSocialLogin.current = true;
            } catch (error) {
                setMessage('Error processing social login');
                toast.error('Error processing social login', {
                    position: 'top-right',
                    autoClose: 4000
                });
                hasHandledSocialLogin.current = true;
            }
        } else if (socialLogin === 'error' && errorMessage) {
            const decodedError = decodeURIComponent(errorMessage);
            setMessage(decodedError);
            toast.error(decodedError, {
                position: 'top-right',
                autoClose: 4000
            });
            hasHandledSocialLogin.current = true;
        }
    }, [location, navigate, from, authenticateWithToken]);

    const generateOTP = async (type) => {
        if (type !== 'mobile') return;

        if (mobileOtpAttempts >= 3) {
            setMessage('You have reached the maximum OTP requests. Please try again later.');
            toast.warning('You have reached the maximum OTP requests. Please try again later.', {
                position: 'top-right',
                autoClose: 4000
            });
            return;
        }

        setIsGeneratingOTP(prev => ({ ...prev, [type]: true }));
        setMessage('');

        try {
            const response = await authAPI.registrationSendOTP({ mobile: formData.mobile });
            setShowMobileOTP(true);
            setOTPData(prev => ({ ...prev, mobile_otp: response.data?.otp || '' }));
            setMobileOtpAttempts(prev => prev + 1);
            const successMessage = response.data?.message || 'OTP sent successfully';
            setMessage(successMessage);
            toast.success(successMessage, {
                position: 'top-right',
                autoClose: 3000
            });
        } catch (err) {
            const data = err.response?.data;
            const errorMessage = data?.message || 'Failed to send OTP';
            setMessage(errorMessage);
            toast.error(errorMessage, {
                position: 'top-right',
                autoClose: 4000
            });
            if (data?.errors?.mobile) {
                setErrors(prev => ({ ...prev, mobile: data.errors.mobile[0] }));
            }
        } finally {
            setIsGeneratingOTP(prev => ({ ...prev, [type]: false }));
        }
    };

    const handleOTPChange = (e) => {
        const { name, value } = e.target;
        setOTPValues(prev => ({
            ...prev,
            [name]: value
        }));
    };

    const verifyOTP = async (type) => {
        if (type !== 'mobile') return false;
        setIsVerifying(prev => ({ ...prev, [type]: true }));
        setMessage('');

        try {
            const response = await authAPI.registrationVerifyOTP({
                mobile: formData.mobile,
                otp: otpValues.mobile_otp_input,
            });
            setOTPVerified(prev => ({ ...prev, mobile: true }));
            const successMessage = response.data?.message || 'Mobile OTP verified successfully';
            setMessage(successMessage);
            toast.success(successMessage, {
                position: 'top-right',
                autoClose: 3000
            });
            return true;
        } catch (err) {
            const data = err.response?.data;
            const errorMessage = data?.message || 'Invalid Mobile OTP. Please try again.';
            setMessage(errorMessage);
            toast.error(errorMessage, {
                position: 'top-right',
                autoClose: 4000
            });
            if (data?.errors?.mobile) {
                setErrors(prev => ({ ...prev, mobile: data.errors.mobile[0] }));
            }
            return false;
        } finally {
            setIsVerifying(prev => ({ ...prev, [type]: false }));
        }
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setLoading(true);
        setErrors({});

        try {
            // Combine first, middle, last name into full name for backend
            const registrationData = {
                name: formData.name,
                email: formData.email,
                phone: formData.mobile,
                password: formData.password,
                password_confirmation: formData.password_confirmation,
                otp: otpValues.mobile_otp_input,
                dob: formData.dob,
                gender: formData.gender,
                referral_code: formData.referral_code || null
            };

            if (!otpVerified.mobile) {
                setErrors(prev => ({ ...prev, mobile: 'Please verify mobile OTP before registering' }));
                setLoading(false);
                return;
            }

            const result = await register(registrationData);
            if (result.success) {
                setMessage('Registration successful! Redirecting...');
                toast.success('Registration successful! Redirecting...', {
                    position: 'top-right',
                    autoClose: 2000
                });
                setTimeout(() => {
                    navigate(from, { replace: true });
                }, 2000);
            } else {
                // Handle validation errors from API response
                if (result.errors && typeof result.errors === 'object') {
                    setErrors(result.errors);
                    // Display validation error message as toast
                    const errorMessage = result.message || 'Validation failed. Please check the form errors.';
                    toast.error(errorMessage, {
                        position: 'top-right',
                        autoClose: 4000
                    });
                    
                    // Show individual field errors as toast too
                    Object.entries(result.errors).forEach(([field, fieldErrors]) => {
                        const errorText = Array.isArray(fieldErrors) ? fieldErrors.join(', ') : fieldErrors;
                        toast.error(`${field}: ${errorText}`, {
                            position: 'top-right',
                            autoClose: 4000
                        });
                    });
                } else {
                    setErrors({ general: result.message });
                    toast.error(result.message || 'Registration failed. Please try again.', {
                        position: 'top-right',
                        autoClose: 4000
                    });
                }
            }
        } catch (error) {
            // console.error('Registration error:', error);
            setErrors({ general: 'Registration failed. Please try again.' });
            toast.error('Registration failed. Please try again.', {
                position: 'top-right',
                autoClose: 4000
            });
        } finally {
            setLoading(false);
        }
    };
  return (
    <>
      <ToastContainer
        position="top-right"
        autoClose={4000}
        hideProgressBar={false}
        newestOnTop={true}
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
      </header><div className="reg-fom wraper1 position-relative">
              {/* Background images */}
              <div className="footm">
                  {Array(4).fill().map((_, i) => (
                      <img key={i} src="/design/assets/footm.png" alt="" />
                  ))}
              </div>

              <div className="xis-testimonial-shape position-absolute">
                  <img src="/design/assets/dot-map.png" alt="" />
              </div>

              {/* Line animation */}
              <div className="line_animation">
                  {Array(7).fill().map((_, i) => (
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

              {/* Register Section */}
              <section id="it-up-contact" className="it-up-contact-section position-relative">
                  <div className="container">
                      <div className="row">
                          <div className="col-lg-5 col-md-6 col-12 col-sm-7 col-xl-5 m-auto">
                              <div className="it-up-form-wrap">
                                  <div className="it-nw-header-logo text-center login-img">
                                      <Link to="/">
                                          <img  src="/design/assets/new/enteK.svg" alt="Logo" className="auth-logo" />
                                      </Link>
                                  </div>
                                  <h3>REGISTER</h3>
                                  <h5>Create your Ente Keralam account</h5>

                                  {message && (
                                      <div className={`alert ${message.toLowerCase().includes('success') ? 'alert-success' : message.toLowerCase().includes('validation') ? 'alert-danger' : 'alert-info'} mb-3`}>
                                          {message}
                                      </div>
                                  )}
                                  
                                  {Object.keys(getRenderableErrors(errors)).length > 0 && !errors.general && (
                                      <div className="alert alert-danger mb-3">
                                          <h6>Following errors occurred:</h6>
                                          <ul className="mb-0">
                                              {Object.entries(getRenderableErrors(errors)).map(([field, fieldErrors]) => (
                                                  <li key={field}>
                                                      <strong>{field}:</strong> {Array.isArray(fieldErrors) ? fieldErrors.join(', ') : fieldErrors}
                                                  </li>
                                              ))}
                                          </ul>
                                      </div>
                                  )}
                                  
                                      {showOTP && (
                                          <div className="alert alert-success mb-3">
                                              <h5>Registration Successful!</h5>
                                              <p>For testing purposes, your Mobile OTP code is:</p>
                                              <p><strong>Mobile OTP:</strong> {otpData.mobile_otp}</p>
                                              <p>Redirecting to home page...</p>
                                          </div>
                                      )}

                                  {errors.general && (
                                      <div className="alert alert-danger mb-3">
                                          {errors.general}
                                      </div>
                                  )}

                                  <div className="social">
                                      <div className="fb mb-2" onClick={() => window.location.href = '/api/auth/facebook'} style={{ cursor: 'pointer' }}>
                                          <img src="/design/assets/facebook.svg" width="28" alt="facebook" /> Sign Up with Facebook
                                      </div>
                                      {/* <div className="gle mr-3" onClick={() => window.location.href = '/api/auth/google'} style={{ cursor: 'pointer' }}>
                                          <img src="/design/assets/google.svg" width="28" alt="google" /> Sign Up with Google
                                      </div> */}
                                  </div>

                                  <p className="mt-3 or">OR</p>

                                  <form onSubmit={handleSubmit}>
                                      {/* Full Name */}
                                      <div className="form-group">
                                          <label className="form-label" htmlFor="name">
                                              Name <span className="text-danger">*</span>
                                          </label>
                                          <div className="form-control-wrap">
                                              <input
                                                  type="text"
                                                  className="form-control form-control-lg"
                                                  id="name"
                                                  name="name"
                                                  value={formData.name}
                                                  onChange={handleChange}
                                                  placeholder="Enter your full name"
                                                  minLength="2"
                                                  maxLength="255"
                                                  autoComplete="name"
                                                  required />
                                              {errors.name && (
                                                  <div className="text-danger small mt-1">{errors.name}</div>
                                              )}
                                          </div>
                                      </div>

                                      {/* Role Selection - Hidden field with default value "public" */}
                                      <input type="hidden" name="role" value="public" />

                                      {/* DOB */}
                                      <div className="form-group">
                                          <label className="form-label" htmlFor="dob">
                                              Date of Birth <span className="text-danger">*</span>
                                          </label>
                                      <div className="form-control-wrap">
                                              <DateOfBirthPicker
                                                  id="dob"
                                                  name="dob"
                                                  value={formData.dob}
                                                  onChange={handleDobChange}
                                                  inputClassName="form-control form-control-lg"
                                                  wrapperClassName="w-100"
                                                  required
                                              />
                                              {errors.dob && (
                                                  <div className="text-danger small mt-1">{errors.dob}</div>
                                              )}
                                          </div>
                                      </div>

                                      {/* Gender */}
                                      <div className="col-12 row mb-2">
                                          <label className="form-label">Gender <span className="text-danger">*</span></label>
                                          {["Male", "Female", "Transgender"].map((g, i) => (
                                              <div key={i} className="col-12 form-group my-1">
                                                  <div className="form-check">
                                                      <input
                                                          type="radio"
                                                          id={`gender-${i}`}
                                                          name="gender"
                                                          className="form-check-input"
                                                          value={g.toLowerCase()}
                                                          checked={formData.gender === g.toLowerCase()}
                                                          onChange={handleChange}
                                                          required />
                                                      <label className="form-check-label" htmlFor={`gender-${i}`}>
                                                          {g}
                                                      </label>
                                                  </div>
                                              </div>
                                          ))}
                                      </div>

                                      {/* Mobile */}
                                      <div className="form-group">
                                          <label className="form-label" htmlFor="mobile">
                                              Mobile <span className="text-danger">*</span>
                                          </label>
                                          <div className="form-control-wrap">
                                                <input
                                                    type="text"
                                                    className="form-control form-control-lg"
                                                    id="mobile"
                                                    name="mobile"
                                                    value={formData.mobile}
                                                    onChange={handleChange}
                                                    placeholder="Enter your mobile"
                                                    maxLength="10"
                                                    required />
                                              <button 
                                                  type="button" 
                                                  className="otp-b btn sub-btn btn-sm my-2 d-flex align-items-center"
                                                  onClick={() => generateOTP('mobile')}
                                                  disabled={!formData.mobile || isGeneratingOTP.mobile || mobileOtpAttempts >= 3}
                                              >
                                                  {isGeneratingOTP.mobile && (
                                                      <div className="spinner-border spinner-border-sm me-2" role="status">
                                                          <span className="visually-hidden">Loading...</span>
                                                      </div>
                                                  )}
                                                  {isGeneratingOTP.mobile
                                                      ? 'Generating...'
                                                      : mobileOtpAttempts >= 3
                                                          ? 'Limit reached'
                                                      : (showMobileOTP ? 'Resend OTP' : 'Generate OTP')}
                                              </button>
                                              {errors.mobile && (
                                                  <div className="text-danger small mt-1">{errors.mobile}</div>
                                              )}
                                          </div>
                                          
                                          {/* Mobile OTP Input Field */}
                                          {showMobileOTP && (
                                              <div className="form-control-wrap mt-2">
                                                  <input
                                                      type="text"
                                                      className="form-control form-control-lg"
                                                      name="mobile_otp_input"
                                                      value={otpValues.mobile_otp_input}
                                                      onChange={handleOTPChange}
                                                      placeholder="Enter Mobile OTP"
                                                      maxLength="6"
                                                      required />
                                                  <button 
                                                      type="button" 
                                                      className={`btn btn-sm btn-otp-verify my-2 d-flex align-items-center ${otpVerified.mobile ? 'btn-secondary' : 'btn-success'}`}
                                                      onClick={() => verifyOTP('mobile')}
                                                      disabled={otpVerified.mobile || isVerifying.mobile}
                                                  >
                                                      {isVerifying.mobile && (
                                                          <div className="spinner-border spinner-border-sm me-2" role="status">
                                                              <span className="visually-hidden">Loading...</span>
                                                          </div>
                                                      )}
                                                      {isVerifying.mobile ? 'Verifying...' : (otpVerified.mobile ? 'Verified ✓' : 'Verify Mobile OTP')}
                                                  </button>
                                                  <div className="info-text">
                                                      Attempts used: {mobileOtpAttempts}/3
                                                  </div>
                                              </div>
                                          )}
                                      </div>

                                      {/* Email (optional) */}
                                      <div className="form-group">
                                          <label className="form-label" htmlFor="email">
                                              Email Address <span className="text-muted">(Optional)</span>
                                          </label>
                                          <div className="form-control-wrap">
                                              <input
                                                  type="email"
                                                  className="form-control form-control-lg"
                                                  id="email"
                                                  name="email"
                                                  value={formData.email}
                                                  onChange={handleChange}
                                                  placeholder="Enter your email address" />
                                              {errors.email && (
                                                  <div className="text-danger small mt-1">{errors.email}</div>
                                              )}
                                          </div>
                                      </div>

                                      {/* Referral Code - Optional */}
                                      <div className="form-group">
                                          <label className="form-label" htmlFor="referral_code">
                                              Referral Code <span className="text-muted">(Optional)</span>
                                          </label>
                                          <div className="form-control-wrap">
                                              <input
                                                  type="text"
                                                  className="form-control form-control-lg"
                                                  id="referral_code"
                                                  name="referral_code"
                                                  value={formData.referral_code}
                                                  onChange={handleChange}
                                                  placeholder="Referral code"
                                                  maxLength="8"
                                                  style={{ textTransform: 'uppercase' }} />
                                              {errors.referral_code && (
                                                  <div className="text-danger small mt-1">{errors.referral_code}</div>
                                              )}
                                              <small className="form-text text-muted">
                                                  Have a referral code? Enter it here!
                                              </small>
                                          </div>
                                      </div>

                                      {/* Password */}
                                      <div className="form-group">
                                          <label className="form-label" htmlFor="password">
                                              Password <span className="text-danger">*</span>
                                          </label>
                                          <div className="form-control-wrap">
                                              <input
                                                  type="password"
                                                  className="form-control form-control-lg"
                                                  id="password"
                                                  name="password"
                                                  value={formData.password}
                                                  onChange={handleChange}
                                                  placeholder="Enter your password"
                                                  required />
                                              {errors.password && (
                                                  <div className="text-danger small mt-1">{errors.password}</div>
                                              )}
                                          </div>
                                      </div>

                                      {/* Confirm Password */}
                                      <div className="form-group">
                                          <label className="form-label" htmlFor="password-confirm">
                                              Confirm Password <span className="text-danger">*</span>
                                          </label>
                                          <div className="form-control-wrap">
                                              <input
                                                  type="password"
                                                  className="form-control form-control-lg"
                                                  id="password-confirm"
                                                  name="password_confirmation"
                                                  value={formData.password_confirmation}
                                                  onChange={handleChange}
                                                  placeholder="Confirm your password"
                                                  required />
                                              {errors.password_confirmation && (
                                                  <div className="text-danger small mt-1">{errors.password_confirmation}</div>
                                              )}
                                          </div>
                                      </div>

                                      {/* Submit */}
                                      <div className="form-group mt-4">
                                          <button
                                              type="submit"
                                              disabled={loading}
                                              className="btn signx btn-lg btn-block w-100 d-flex align-items-center justify-content-center">
                                              {loading && (
                                                  <div className="spinner-border spinner-border-sm me-2" role="status">
                                                      <span className="visually-hidden">Loading...</span>
                                                  </div>
                                              )}
                                              {loading ? 'REGISTERING...' : 'REGISTER'}
                                          </button>
                                      </div>
                                  </form>

                                  {/* Sign in link */}
                                  <div className="text-center">
                                      <p className="regi">
                                          Already have account ?{" "}
                                          <span><Link to="/login">Sign in instead</Link></span>
                                      </p>
                                      <Link to="/" className="btn btn-outline-ente-home w-100 mt-2 mb-5 d-flex align-items-center justify-content-center gap-2">
                                          <FaHome /> Back to Home
                                      </Link>
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
              </section>

              {/* Footer */}
              <section id="it-up-footer" className="it-up-footer-section position-relative">
                  <div className="it-up-footer-copyright text-center pera-content">
                      <div className="container">
                          <p>© {new Date().getFullYear()} Government of Kerala - Ente Keralam Programme. Developed by C-DIT. All rights reserved.</p>
                      </div>
                  </div>
              </section>
          </div></>
  );
}
