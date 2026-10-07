
import { useState, useEffect } from 'react';
import { footersAPI } from '../services/api';
import { useLanguage } from './LanguageContext';

export default function Footer() {
  
  const [footerData, setFooterData] = useState(null);
  const [loading, setLoading] = useState(true);
  const { language } = useLanguage();

  useEffect(() => {
    const fetchFooterData = async () => {
      try {
        const response = await footersAPI.getAll();
        if (response.data.status && response.data.data) {
          setFooterData(response.data.data);
        }
      } catch (error) {
        console.error('Error fetching footer data:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchFooterData();
  }, []);

  // Helper function to organize footer data
  const getFooterItems = (filterFn) => {
    if (!footerData) return [];
    return footerData.filter(filterFn);
  };

  // Helper to get display text based on language
  const getDisplayText = (enText, malText) => {
    return language === 'ml' ? (malText || enText) : enText;
  };

  const linkItems = getFooterItems(item => ['Home', 'Activity', 'Social', 'Downloads'].includes(item.entitle));
  const activityItems = getFooterItems(item => ['Discussions & Forums', 'Polls & Surveys', 'Interactive Quizzes', 'Community Tasks', 'News & Updates'].includes(item.entitle));
  const contactItems = getFooterItems(item => item.entitle === 'Ente Keralam' || item.entitle.includes('@') || item.entitle.includes('+'));
  const description = getFooterItems(item => item.entitle && item.entitle.startsWith('Ente Keralam is'))[0];
  const legalLinks = [
    { label: 'Privacy Policy', href: '/privacy-policy' },
    { label: 'Data Deletion', href: '/data-deletion' },
  ];

  return (
    <section
      id="it-up-footer"
      className="it-up-footer-section position-relative"
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
                        src="/design/assets/footer/logo-footer.svg"
                        alt="Ente Keralam"
                      />
                    </a>
                  </div>
                  <p>
                    {loading ? (
                      'Loading...'
                    ) : description ? (
                      getDisplayText(description.entitle, description.maltitle)
                    ) : (
                      'Ente Keralam is Kerala\'s premier citizen engagement platform developed by C-DIT for the Government of Kerala. Citizens actively participate in democratic processes through discussions, polls, quizzes, and community initiatives. Join us in building a better Kerala together.'
                    )}
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
                    {loading ? (
                      <li>Loading...</li>
                    ) : linkItems.length > 0 ? (
                      linkItems.map((item) => (
                        <li key={item.id}>
                          <i className="fas fa-chevron-right"></i>
                          <a href={item.link_text || '#'}>{getDisplayText(item.entitle, item.maltitle)}</a>
                        </li>
                      ))
                    ) : (
                      <li>
                        <i className="fas fa-chevron-right"></i> <a href="/">Home</a>
                      </li>
                    )}
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
                    {loading ? (
                      <li>Loading...</li>
                    ) : activityItems.length > 0 ? (
                      activityItems.map((item) => (
                        <li key={item.id}>
                          <a href={item.link_text || '#'}>{getDisplayText(item.entitle, item.maltitle)}</a>
                        </li>
                      ))
                    ) : (
                      <>
                        <li><a href="#!">Discussions & Forums</a></li>
                        <li><a href="#!">Polls & Surveys</a></li>
                        <li><a href="#!">Interactive Quizzes</a></li>
                        <li><a href="#!">Community Tasks</a></li>
                        <li><a href="#!">News & Updates</a></li>
                      </>
                    )}
                  </ul>
                </div>
              </div>
            </div>

            {/* Newsletter + Socials */}
            <div className="col-lg-3 col-md-6">
              <div className="it-up-footer-widget headline-1 pera-content">
                <div className="it-up-footer-info-widget it-up-footer-newslatter-widget">
                  <h3 className="widget-title">Contact Us</h3>
                  <div className="ul-li-block margin-ul-li">
                    <ul>
                    {loading ? (
                      <li>Loading...</li>
                    ) : contactItems.length > 0 ? (
                      contactItems.map((item) => (
                        <li key={item.id}>
                          {item.entitle === 'Ente Keralam' && (
                            <>
                              <i className="fas fa-map-marker-alt"></i>
                              <a href="#">{getDisplayText(item.entitle, item.maltitle)}</a>
                            </>
                          )}
                          {item.entitle.includes('-') && !item.entitle.includes('@') && (
                            <>
                              <i className="fas fa-phone"></i>
                              <a href={`tel:${item.entitle}`}>{getDisplayText(item.entitle, item.maltitle)}</a>
                            </>
                          )}
                          {item.entitle.includes('@') && (
                            <>
                              <i className="fas fa-envelope"></i>
                              <a href={`mailto:${item.entitle}`}>{getDisplayText(item.entitle, item.maltitle)}</a>
                            </>
                          )}
                        </li>
                      ))
                    ) : (
                      <>
                        <li>
                          <i className="fas fa-map-marker-alt"></i>
                          <a href="#">Ente Keralam</a>
                        </li>
                      </>
                    )}
                    </ul>
                  </div>

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

                <div className="it-up-footer-info-widget ul-li-block margin-ul-li mt-3">
                  <h3 className="widget-title">Legal</h3>
                  <ul>
                    {legalLinks.map(link => (
                      <li key={link.href}>
                        <i className="fas fa-chevron-right"></i>
                        <a href={link.href}>{link.label}</a>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Copyright */}
      <div className="it-up-footer-copyright text-center pera-content">
        <div className="container">
          <p>
            © {new Date().getFullYear()} Government of Kerala - Ente Keralam Programme. Developed by C-DIT. All rights reserved.
            <span>&nbsp;Last Update on : {new Date().toLocaleDateString('en-IN')}</span>
          </p>
        </div>
      </div>
    </section>
  );
}
