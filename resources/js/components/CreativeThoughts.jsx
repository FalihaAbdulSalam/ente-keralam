"use client"
import { useState, useEffect } from "react"
import { creativethoughtsAPI } from "../services/api"

export default function CreativeThoughts() {
  const [galleryItems, setGalleryItems] = useState([])
  const [activeIndex, setActiveIndex] = useState(0)
  const [loading, setLoading] = useState(true)

  // Default gallery items fallback
  const DEFAULT_GALLERY_ITEMS = [
    "/design/assets/poster/poster-01.jpg",
    "/design/assets/poster/poster-02.jpg",
    "/design/assets/poster/poster-03.jpg",
  ]

  useEffect(() => {
    const fetchCreativeThoughts = async () => {
      try {
        const response = await creativethoughtsAPI.getAll()
        if (response.data.status && response.data.data && response.data.data.length > 0) {
          const items = response.data.data.map((item) => item.poster)
          setGalleryItems(items)          
        } else {
          setGalleryItems(DEFAULT_GALLERY_ITEMS)
        }
      } catch (error) {
        console.error('Error fetching creative thoughts:', error)
        setGalleryItems(DEFAULT_GALLERY_ITEMS)
      } finally {
        setLoading(false)
      }
    }

    fetchCreativeThoughts()
  }, [])

  const items = galleryItems.length > 0 ? galleryItems : DEFAULT_GALLERY_ITEMS

  return (
    <section
      className="daily it-up-contact-section position-relative "
      style={{ background: "#33cc001c" }}
    >
      {/* Decorative Images */}
      <img className="quizz" src="/design/assets/background/qs-02.svg" alt="quiz" />
      <span className="it-up-service-shape position-absolute deco1">
        <img src="/design/assets/vect/s-shape1.png" alt="" />
      </span>
      <span className="it-up-service-shape position-absolute deco2">
        <img src="/design/assets/vect/s-shape2.png" alt="" />
      </span>
      <span className="it-up-service-shape position-absolute deco4">
        <img src="/design/assets/vect/s-shape4.png" alt="" />
      </span>

      {/* Scoped styles for hover-expand gallery driven by active state */}
      <style>{`
        .gallery-container {
          display: flex;
          align-items: stretch;
          width: 100%;
          margin-bottom: 42px;
        }
        .gallery-container .item {
          position: relative;
          flex: 0.35 1 0; /* thin by default */
          height: 420px;
          overflow: hidden;
          transition: flex 300ms ease;
          box-shadow: 0 6px 16px rgba(0,0,0,0.08);
          background: #e9eef3;
        }
        .gallery-container .item img {
          width: 100%;
          height: 100%;
          object-fit: cover;
          transition: transform 400ms ease;
          display: block;
        }
        /* Active item expands */
        .gallery-container .item.active { 
          flex: 3 1 0; 
        }
        .gallery-container .item.active img {
          transform: scale(1.02);
        }
        /* Small screens: stack and disable widths */
        @media (max-width: 767.98px) {
          .gallery-container { height: 95px;}
          .gallery-container .item { height: 110px; }
        }
        .it-up-contact-section {
          overflow: hidden;
        }
      `}</style>

      <div className="container-fluid">
        <div className="col-md-9 mx-auto">
          {/* Section Title */}
          <div
            className="col-lg-4 col-md-4 col-12 wow fadeInLeft pl-0"
            data-wow-delay="0ms"
            data-wow-duration="1500ms"
          >
            <div className="it-nw-section-title headline pera-content">
              <span className="it-nw-title-tag">Express & Inspire</span>
              <h2>Creative thoughts</h2>
            </div>
          </div>

          {/* Gallery */}
          <div className="gallery-container mt-3">
            {loading ? (
              <div style={{ width: '100%', textAlign: 'center', padding: '20px' }}>
                Loading gallery...
              </div>
            ) : (
              items.map((src, index) => (
                <div
                  className={`item ${index === activeIndex ? "active" : ""}`}
                  key={index}
                  onMouseEnter={() => setActiveIndex(index)}
                >
                  <img src={src} alt={`Creative ${index + 1}`} />
                </div>
              ))
            )}
          </div>

          {/* Button */}
          <div className="it-nw-btn mt-4 d-flex justify-content-center">
            <a className="d-flex justify-content-center wow align-items-center" href="/creative-gallery">View All</a>
          </div>
        </div>
      </div>
    </section>
  )
}
