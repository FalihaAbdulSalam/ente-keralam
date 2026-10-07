
export default function HeaderMain() {
  return (
    <div
      className="it-nw-header-main justify-content-between d-none d-sm-flex"
      style={{ position: "sticky", top: 0, zIndex: 99 }}
    >
      {/* Uncomment if logo needed */}
      {/* <div className="it-nw-header-logo">
        <a href="#">
          <img src="/design/assets/new/enteK.svg" alt="" />
        </a>
      </div> */}
      
      <div className="it-nw-main-menu-wrap d-flex align-items-center w-100 justify-content-between">
        <nav className="it-nw-main-navigation ul-li">
          <ul id="main-nav" className="navbar-nav text-capitalize clearfix">
            <li>
              <a href="/">Home</a>
            </li>
            <li>
              <a href="/" target="_blank" rel="noopener noreferrer">
                Activity
              </a>
            </li>
            <li>
              <a href="/insight">Insights</a>
            </li>
            <li>
              <a href="/social">Socials</a>
            </li>

            {/* Hidden dropdowns */}
            <li className="dropdown d-none">
              <a href="#"> Know Your State</a>
              <ul className="dropdown-menu clearfix">
                <li>
                  <a href="contact.html" target="_blank" rel="noopener noreferrer">
                    Contact 1
                  </a>
                </li>
                <li>
                  <a href="contact-2.html" target="_blank" rel="noopener noreferrer">
                    Contact 2
                  </a>
                </li>
              </ul>
            </li>
            <li className="dropdown d-none">
              <a href="#">Insights</a>
              <ul className="dropdown-menu clearfix">
                <li>
                  <a href="portfolio.html" target="_blank" rel="noopener noreferrer">
                    Portfolio Filter
                  </a>
                </li>
                <li>
                  <a href="portfolio-2.html" target="_blank" rel="noopener noreferrer">
                    Portfolio Page 2
                  </a>
                </li>
                <li>
                  <a href="project-single.html" target="_blank" rel="noopener noreferrer">
                    Portfolio Details
                  </a>
                </li>
              </ul>
            </li>

            <li>
              <a href="faq.html">FAQ's</a>
            </li>

            {/* Search button */}
            <li>
              <button type="button" className="apldg-search-btn">
                <i className="fas fa-search"></i>
              </button>
            </li>
          </ul>
        </nav>

        <div className="d-flex align-items-center">
          <div className="it-nw-btn text-center">
            <a
              className="d-flex justify-content-center align-items-center"
              href="/login"
            >
              Login <i className="fas fa-arrow-right"></i>
            </a>
          </div>

          {/* WhatsApp link (commented out in original) */}
          {/* <a className="whatsapp" href="#">
            <img src="/design/assets/wtss.png" alt="" />
            Join our <br /> <span>WhatsApp</span>
          </a> */}

          <a href="#" className="ml-2 whts" style={{ height: "45px" }}>
            <img src="/design/assets/wts.png" alt="" style={{ height: "40px" }} />
          </a>
        </div>
      </div>
    </div>
  );
}
