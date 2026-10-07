import { useEffect, useState } from "react";
import { creativethoughtsAPI } from "../services/api";
import { useLanguage } from "./LanguageContext";
import UIkit from "uikit";
import Icons from "uikit/dist/js/uikit-icons";
import 'uikit/dist/css/uikit.min.css';

UIkit.use(Icons);

const CreativeGallery = () => {
  const { language } = useLanguage();
  const [galleryItems, setGalleryItems] = useState([]);
  const [loading, setLoading] = useState(true);

  const DEFAULT_GALLERY_ITEMS = [
    // fallback images if API fails
  ];

  // Helper to get display text based on language
  const getDisplayText = (enText, malText) => {
    return language === 'ml' ? (malText || enText) : enText;
  };

  useEffect(() => {
    const fetchCreativeThoughts = async () => {
      try {
        const response = await creativethoughtsAPI.getAll();

        if (response.data.status && response.data.data?.length > 0) {
          const items = response.data.data.map((item, index) => ({
            id: item.id || index,
            img: item.poster,
            caption: getDisplayText(item.entitle || `Creative Thought ${index + 1}`, item.maltitle),
          }));
          setGalleryItems(items);
        } else {
          setGalleryItems(DEFAULT_GALLERY_ITEMS);
        }
      } catch (error) {
        console.error("Error fetching creative thoughts:", error);
        setGalleryItems(DEFAULT_GALLERY_ITEMS);
      } finally {
        setLoading(false);
      }
    };

    fetchCreativeThoughts();
  }, []);

  // Proper UIkit re-init (NO duplicate lightbox items)
  useEffect(() => {
    if (!loading && galleryItems.length > 0) {
      UIkit.update(); // this is enough when uk-lightbox is on the wrapper
    }
  }, [loading, galleryItems]);

  return (
    <>
      {/* Breadcrumb Section */}
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="containerfluid">
          <div className="col-md-11 mx-auto">
            <div className="breadcurmb-title">
              <h2>Insights</h2>
            </div>
            <div className="breadcurmb-item-list ul-li">
              <ul className="saasio-page-breadcurmb">
                <li><a href="#">Home</a></li>
                <li><a href="#">FAQ's</a></li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* Blog / Gallery Section */}
      <section id="news-feed" className="news-feed-section position-relative">
        <div className="it-nw-side-bg text-center position-absolute">
          <img src="/design/assets/its-2/side-line.png" alt="" />
        </div>

        <div className="container-fluid">
          <div className="blog-feed-content">
            <div className="col-md-11 mx-auto">
              <div className="row">
                <div className="col-md-12">
                  <div className="saasio-blog-details-content">
                    
                    <div className="it-nw-section-title headline pera-content mb-3">
                      <span className="it-nw-title-tag">Progress</span>
                      <h2>Creative Thoughts</h2>
                    </div>

                    {/* The FIX: uk-lightbox placed on wrapper */}
                    {/* <section 
                      className="creativeGal"
                      uk-lightbox="animation: scale"
                    >
                      {loading ? (
                        <div style={{ textAlign: "center", padding: "40px" }}>
                          <p>Loading gallery...</p>
                        </div>
                      ) : (
                        <div
                          className="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1"
                          uk-grid
                        >
                          {galleryItems.map((item) => (
                            <div className="gal" key={item.id}>
                              <a className="uk-inline" href={item.img} data-caption={item.caption}>
                                <img
                                  src={item.img}
                                  alt={item.caption}
                                  style={{ width: "100%", height: "auto" }}
                                />
                              </a>

                              <div className="shr1">
                                <p className="galtx">{item.caption}</p>

                                <div className="ico">
                                  <a href="#" onClick={(e) => e.preventDefault()}>
                                    <img
                                      src="/design/assets/social/facebook.svg"
                                      width="24"
                                      alt="Facebook"
                                    />
                                  </a>
                                  <a href="#" onClick={(e) => e.preventDefault()}>
                                    <img
                                      src="/design/assets/social/insta.svg"
                                      width="24"
                                      alt="Instagram"
                                    />
                                  </a>
                                </div>
                              </div>
                            </div>
                          ))}
                        </div>
                      )}
                    </section> */}

                    <section 
  className="creativeGal"
  uk-lightbox="animation: scale; selector: .lightbox-item"
>
  {loading ? (
    <div style={{ textAlign: "center", padding: "40px" }}>
      <p>Loading gallery...</p>
    </div>
  ) : (
    <div
      className="uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1"
      uk-grid
    >
      {galleryItems.map((item) => (
        <div className="gal" key={item.id}>
          
          {/* ONLY these anchors will be included in the Lightbox */}
          <a
            className="uk-inline lightbox-item"
            href={item.img}
            data-caption={item.caption}
          >
            <img
              src={item.img}
              alt={item.caption}
              style={{ width: "100%", height: "auto" }}
            />
          </a>

          <div className="shr1">
            <p className="galtx">{item.caption}</p>
            <div className="ico">
              <a href="#" onClick={(e) => e.preventDefault()}>
                <img src="/design/assets/social/facebook.svg" width="24" alt="Facebook" />
              </a>
              <a href="#" onClick={(e) => e.preventDefault()}>
                <img src="/design/assets/social/insta.svg" width="24" alt="Instagram" />
              </a>
            </div>
          </div>

        </div>
      ))}
    </div>
  )}
</section>

                    {/* END GALLERY */}

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default CreativeGallery;
