"use client";
import { useState, useEffect } from "react";
import {
  FaChevronUp,
  FaBars,
  FaTimesCircle,
  FaSearch,
  FaArrowRight,
} from "react-icons/fa";
import { useLanguage } from "./LanguageContext";

export default function InnerHeader() {
  const [loading, setLoading] = useState(true);
  const [menuOpen, setMenuOpen] = useState(false);
  const { language, toggleLanguage } = useLanguage();

  useEffect(() => {
    // Simulate loading delay (2s), or you can remove setTimeout if fetching real data
    const timer = setTimeout(() => {
      setLoading(false);
    }, 2000);

    return () => clearTimeout(timer);
  }, []);
   const toggleMobileMenu = () => {
    setMenuOpen(!menuOpen);
  };

  return (
    <>
      {/* Preloader */}
      {loading && <div id="preloader"></div>}
<div className="up">
		<a href="#" className="scrollup text-center"><i className="fas fa-chevron-up"></i></a>
	</div>
      <div className="apldg-header-form">
        <div className="apldg-form-overlay"></div>
        <form action="#">
          <input type="text" placeholder="Search..." />
          <button type="submit">Go</button>
        </form>
      </div>

      <div className="up">
        <a href="#" className="scrollup text-center">
          <FaChevronUp />
        </a>
      </div>

      {/* Header Section */}
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
                {/* <li>
                <a href="#">Skip to Main content</a>
              </li> */}
                <li>
                  <a
                    type="button"
                    onClick={() => toggleLanguage("ml")}
                    style={{
                      background: "none",
                      border: "none",
                      cursor: "pointer",
                      padding: 0,
                    }}
                  >
                    <span
                      style={{
                        fontWeight: language === "ml" ? "bold" : "normal",
                        opacity: language === "ml" ? 1 : 0.6,
                        transition: "all 0.2s ease",
                      }}
                    >
                      മലയാളം
                    </span>
                  </a>
                </li>
                <li style={{ marginLeft: "10px" }}>
                  <a
                    type="button"
                    onClick={() => toggleLanguage("en")}
                    style={{
                      background: "none",
                      border: "none",
                      cursor: "pointer",
                      padding: 0,
                    }}
                  >
                    <span
                      style={{
                        fontWeight: language === "en" ? "bold" : "normal",
                        opacity: language === "en" ? 1 : 0.6,
                        transition: "all 0.2s ease",
                      }}
                    >
                      English
                    </span>
                  </a>
                </li>
                <li className="dropdown">
                  <img src="/design/assets/share.png" alt="" width="20" />
                  <ul className="dropdown-content">
                    <li>
                      <a href="#">
                        <img src="/design/assets/youtube.png" alt="" />
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <img src="/design/assets/facebook.png" alt="" />
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <img src="/design/assets/twitter.png" alt="" />
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <img src="/design/assets/instagram.png" alt="" />
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </div>

          <div className="it-nw-header-main d-flex justify-content-between align-items-center">
            {/* Logo */}
            <div className="it-nw-header-logo">
              <a href="/">
                <img src="/design/assets/new/enteK.svg" alt="Logo" />
              </a>
            </div>

            {/* Main Navigation */}
            <div className="it-nw-main-menu-wrap d-flex align-items-center w-100 justify-content-end">
              <nav className="it-nw-main-navigation ul-li">
                <ul
                  id="main-nav"
                  className="navbar-nav text-capitalize clearfix"
                >
                  <li>
                    <a href="/" target="_blank" rel="noreferrer">
                      Home
                    </a>
                  </li>
                  <li>
                    <a href="/insight">Insights</a>
                  </li>
                  <li>
                    <a href="/social">Socials</a>
                  </li>

                  {/* Hidden Dropdown - Know Your State */}
                  <li className="dropdown d-none">
                    <a href="#">Know Your State</a>
                    <ul className="dropdown-menu clearfix">
                      <li>
                        <a href="contact.html" target="_blank" rel="noreferrer">
                          Contact 1
                        </a>
                      </li>
                      <li>
                        <a
                          href="contact-2.html"
                          target="_blank"
                          rel="noreferrer"
                        >
                          Contact 2
                        </a>
                      </li>
                    </ul>
                  </li>

                  {/* Hidden Dropdown - Insights */}
                  <li className="dropdown d-none">
                    <a href="#">Insights</a>
                    <ul className="dropdown-menu clearfix">
                      <li>
                        <a
                          href="portfolio.html"
                          target="_blank"
                          rel="noreferrer"
                        >
                          Portfolio Filter
                        </a>
                      </li>
                      <li>
                        <a
                          href="portfolio-2.html"
                          target="_blank"
                          rel="noreferrer"
                        >
                          Portfolio Page 2
                        </a>
                      </li>
                      <li>
                        <a
                          href="project-single.html"
                          target="_blank"
                          rel="noreferrer"
                        >
                          Portfolio Details
                        </a>
                      </li>
                    </ul>
                  </li>

                  <li>
                    <a href="faq.html">FAQ's</a>
                  </li>

                  {/* Search Button */}
                  <li>
                    <button type="button" className="apldg-search-btn">
                      <i className="fas fa-search"></i>
                    </button>
                  </li>
                </ul>
              </nav>

              {/* Login and WhatsApp Section */}
              <div className="d-flex align-items-center">
                <div className="it-nw-btn text-center">
                  <a
                    className="d-flex justify-content-center align-items-center"
                    href="/login"
                  >
                    Login <i className="fas fa-arrow-right"></i>
                  </a>
                </div>

                {/* WhatsApp Button */}
                <a href="#" className="ml-2 whts" style={{ height: "45px" }}>
                  <img src="/design/assets/wts.png" alt="WhatsApp" style={{ height: "40px" }} />
                </a>
              </div>
            </div>
          </div>

          <div className="it_nw_mobile_menu relative-position">
            <div
              className="it_nw_mobile_menu_button it_nw_open_it_nw_mobile_menu"
              onClick={toggleMobileMenu}
            >
              <i
                className={`fas ${menuOpen ? "fa-times-circle" : "fa-bars"}`}
              ></i>
            </div>

            {/* Mobile Menu Wrapper */}
            <div
              className={`it_nw_it_nw_mobile_menu_wrap ${
                menuOpen ? "it_nw_it_nw_mobile_menu_on" : ""
              }`}
            >
              <div className="it_nw_it_nw_mobile_menu_content">
                <div
                  className="it_nw_it_nw_mobile_menu_close"
                  onClick={toggleMobileMenu}
                ><i className="far fa-times-circle"></i>
                </div>
                <div className="m-brand-logo text-center">
                  <img src="/design/assets/logo.svg" alt="" />
                </div>
                <nav className="main-navigation it_nw_it_nw_mobile_menu-dropdown clearfix ul-li">
                  <ul
                    id="main-nav"
                    className="navbar-nav text-capitalize clearfix"
                  >
                    <li>
              <a href="/">Home</a>
            </li>
                    <li>
                      <a href="/" target="_blank">
                        Activity
                      </a>
                    </li>
                    <li>
                      <a href="/insight">Insights</a>
                    </li>
                    <li>
                      <a href="/social">Socials</a>
                    </li>
                    <li className="dropdown d-none">
                      <a href="#"> Know Your State</a>
                      <ul className="dropdown-menu clearfix">
                        <li>
                          <a target="_blank" href="contact.html">
                            Contact 1
                          </a>
                        </li>
                        <li>
                          <a target="_blank" href="contact-2.html">
                            Contact 2
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li className="dropdown d-none">
                      <a href="#">Insights</a>
                      <ul className="dropdown-menu clearfix">
                        <li>
                          <a target="_blank" href="portfolio.html">
                            Portfolio Filter
                          </a>
                        </li>
                        <li>
                          <a target="_blank" href="portfolio-2.html">
                            Portfolio Page 2
                          </a>
                        </li>
                        <li>
                          <a target="_blank" href="project-single.html">
                            Portfolio Details
                          </a>
                        </li>
                      </ul>
                    </li>
                    <li>
                      <a href="faq.html">FAQ's</a>
                    </li>
                    {/* <li>
                    <button type="button" className="apldg-search-btn">
                      <i className="fas fa-search"></i>
                    </button>
                  </li> */}
                  </ul>
                </nav>
                <div className="it_nw_mobile_search_wrapper">
                  <form
                    className="it_nw_mobile_search_form"
                    onSubmit={(e) => e.preventDefault()}
                  >
                    <input
                      type="text"
                      placeholder="Search..."
                      className="it_nw_mobile_search_input"
                    />
                    <a href="/" className="it_nw_mobile_search_btn">
                      <i className="fas fa-search"></i>
                    </a>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </header>
    </>
  );
}
