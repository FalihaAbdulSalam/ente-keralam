import { useState } from "react";
import { Link, useNavigate } from "react-router-dom";
import { FaChevronUp, FaBars, FaTimesCircle, FaSearch, FaArrowRight } from "react-icons/fa";
import { useAuth } from './App';

export default function LoginPage() {
  const [formData, setFormData] = useState({
    email: '',
    password: ''
  });
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);
  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});

    const result = await login(formData);
    
    if (result.success) {
      navigate('/');
    } else {
      setErrors({ general: result.message });
    }
    
    setLoading(false);
  };

  const handleSocialLogin = (provider) => {
    window.location.href = `/api/auth/${provider}`;
  };

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
                <li><Link to="/"><span>മലയാളം</span></Link></li>
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
                  <div className="it-nw-header-logo text-center">
                    <Link to="/"><img src="/design/assets/logo.svg" alt="" /></Link>
                  </div>

                  <h3>Login to Your Account</h3>
                  
                  {errors.general && (
                    <div className="alert alert-danger">{errors.general}</div>
                  )}

                  <form onSubmit={handleSubmit}>
                    <div className="row">
                      <div className="col-12">
                        <input
                          type="email"
                          placeholder="Enter your email"
                          value={formData.email}
                          onChange={(e) => setFormData({...formData, email: e.target.value})}
                          required
                        />
                      </div>
                      <div className="col-12">
                        <input
                          type="password"
                          placeholder="Enter your password"
                          value={formData.password}
                          onChange={(e) => setFormData({...formData, password: e.target.value})}
                          required
                        />
                      </div>
                      <div className="col-12">
                        <div className="it-nw-btn d-flex justify-content-center">
                          <button type="submit" disabled={loading}>
                            {loading ? 'Logging in...' : 'Login'}
                          </button>
                        </div>
                      </div>
                    </div>
                  </form>

                  <div className="social-login mt-4">
                    <h5 className="text-center">Or login with</h5>
                    <div className="d-flex justify-content-center gap-3 mt-3">
                      <button 
                        onClick={() => handleSocialLogin('google')}
                        className="btn btn-outline-primary"
                      >
                        Google
                      </button>
                      <button 
                        onClick={() => handleSocialLogin('facebook')}
                        className="btn btn-outline-primary"
                      >
                        Facebook
                      </button>
                    </div>
                  </div>

                  <div className="text-center mt-4">
                    <p>Don't have an account? <Link to="/register">Register here</Link></p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>
      </div>
    </>
  );
}
