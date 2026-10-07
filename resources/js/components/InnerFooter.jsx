
export default function InnerFooter() {
  return (
    <section
      id="it-up-footer"
      className="it-up-footer-section position-relative"
      // style={{ background: "#ff009912" }}
    >
      <div className="container">
        <div className="it-up-footer-content-wrap">
          <div className="row">
            {/* Logo + Description */}
            <div className="col-lg-3 col-md-6">
              <div className="it-up-footer-widget headline-1 pera-content">
                <div className="it-up-footer-logo-widget it-up-headline pera-content">
                  <div className="it-up-footer-logo">
                    <a href="/">
                      <img
                        className="footer-logo-white"
                        //  src="/design/assets/its/logo/logo2.png"
                        src="/design/assets/footer/logo-footer.svg"
                        alt="Logo"
                      />
                    </a>
                  </div>
                  <p>
                    Lorem ipsum dolor sit amet, consectetur adipienda vero
                    perferendis architecto! Quasi eligendi unde perspiciatis
                    remccc.
                  </p>
                  <a
                    className="footer-logo-btn text-center text-capitalize"
                    href="#"
                    style={{ color: "#666666" }}
                  >
                    Get In Touch <i className="fas fa-arrow-right"></i>
                  </a>
                </div>
              </div>
            </div>

            {/* Help & Support */}
            <div className="col-lg-3 col-md-6">
              <div className="it-up-footer-widget headline-1 pera-content">
                <div className="it-up-footer-info-widget ul-li-block margin-ul-li">
                  <h3 className="widget-title">Link</h3>
                  <ul>
                    <li>
                      <i className="fas fa-chevron-right"></i>{" "}
                      <a href="/">Home</a>
                    </li>
                   
                    <li>
                      <i className="fas fa-chevron-right"></i>
                      <a href="/">Activity</a>
                    </li>
                   
                    <li>
                      <i className="fas fa-chevron-right"></i>
                      <a href="/social">Social</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            {/* Activities */}
            <div className="col-lg-3 col-md-6">
              <div className="it-up-footer-widget headline-1 pera-content">
                <div className="it-up-footer-info-widget ul-li-block margin-ul-li">
                  <h3 className="widget-title">Activities</h3>
                  <ul className="links">
                    <li>
                      <a href="!#">Discussion</a>
                    </li>
                    <li>
                      <a href="!#">Poll</a>
                    </li>
                    <li>
                      <a href="!#">Microsites 1</a>
                    </li>
                    <li>
                      <a href="!#">Microsites 2</a>
                    </li>
                    <li>
                      <a href="!#">Microsites 3</a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>

            {/* Newsletter + Socials */}
 <div className="col-lg-3 col-md-6">
              <div className="it-up-footer-widget headline-1 pera-content">
                <div className="it-up-footer-info-widget it-up-footer-newslatter-widget">
                  <h3 className="widget-title">Link</h3>
                  <div className="ul-li-block margin-ul-li"> <ul>
                    <li>
                      <i className="fas fa-map-marker-alt"></i>{" "}
                      <a href="#">30 Commercial Road, Fratton, Australia</a>
                    </li>
                    <li>
                      <i className="fas fa-phone"></i>
                      <a href="#">1-888-452-1505</a>
                    </li>
                    <li>
                      <i className="fas fa-envelope"></i>
                      <a href="#">sample@mail.com</a>
                    </li>
                  </ul></div>
                 
                  <div className="it-up-footer-social ul-li">
                    <ul className="social2">
                      <li>
                        <a href="#">
                          <img
                            src="/design/assets/social/facebook.svg"
                            alt="Facebook"
                            width="30"
                          />
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <img
                            src="/design/assets/social/youtube.svg"
                            alt="YouTube"
                            width="30"
                          />
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <img
                            src="/design/assets/social/insta.svg"
                            alt="Instagram"
                            width="30"
                          />
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <img
                            src="/design/assets/social/twitter.svg"
                            alt="Twitter"
                            width="30"
                          />
                        </a>
                      </li>
                      <li>
                        <a href="#">
                          <img
                            src="/design/assets/social/whatsapp.svg"
                            alt="WhatsApp"
                            width="30"
                          />
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

           
          </div>
        </div>
      </div>

      {/* Footer Copyright */}
      <div className="it-up-footer-copyright text-center pera-content">
        <div className="container">
          <p>
            © 2024 C-DIT. All rights reserved.
            <span>&nbsp;Last Update on : 12/02/2024</span>
          </p>
        </div>
      </div>
    </section>
  );
}
