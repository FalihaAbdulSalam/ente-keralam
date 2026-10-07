import { useState } from "react";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import "./TestimonialSection.css"; 
import { FaArrowRight, FaQuoteLeft, FaUsers, FaHandshake, FaLightbulb } from "react-icons/fa";

const WinnersSection = () => {
  const [activeIndex, setActiveIndex] = useState(0);

  // Citizen success stories / testimonials
  const testimonials = [
    {
      id: 1,
      name: "Citizen Engagement",
      icon: <FaUsers className="testimonial-icon" />,
      title: "Community Participation",
      description: "Join thousands of citizens actively participating in governance through polls, surveys, and community discussions.",
    },
    {
      id: 2,
      name: "Government Initiatives",
      icon: <FaHandshake className="testimonial-icon" />,
      title: "Bridging the Gap",
      description: "A platform that connects citizens directly with government initiatives, policies, and development programs.",
    },
    {
      id: 3,
      name: "Your Voice Matters",
      icon: <FaLightbulb className="testimonial-icon" />,
      title: "Make an Impact",
      description: "Share your ideas, take pledges, participate in quizzes, and contribute to building a better community.",
    },
  ];

  const settings = {
    dots: false,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3000,
    slidesToShow: 3,
    beforeChange: (current, next) => setActiveIndex(next),
    slidesToScroll: 1,
    vertical: true,
    verticalSwiping: true,
    arrows: false,
    pauseOnHover: true,
    centerMode: true, 
    centerPadding: "0", 
  };

  return (
    <section
      id="xis-testimonial"
      className="xis-testimonial-section position-relative"
    >
      <div className="xis-testimonial-shape position-absolute">
        <img src="/design/assets/dot-map.png" alt="" />
      </div>
      <div className="winner position-absolute">
        <img src="/design/assets/winn32.png" alt="" />
      </div>
      <div className="container">
        <div className="xis-testimonial-content">
          <div className="row">
            <div className="col-lg-5">
              <div className="xis-testimonial-text">
                <div
                  className="xis-section-title headline pera-content wow fadeFromUp"
                  data-wow-delay="0ms"
                  data-wow-duration="1500ms"
                >
                  <div className="it-nw-section-title headline pera-content">
                    <span className="it-nw-title-tag">Empowering Citizens</span>
                    <h2>Why get involved?</h2>
                  </div>
                </div>
                <p>
                  Be a part of the digital transformation in governance. Your participation helps shape policies and build a better future for everyone.
                </p>

                <div className="xis-testimonial-feature-wrapper d-flex justify-content-between">
                  <div
                    className="xis-testimonial-feature-item headline pera-content wow fadeFromUp"
                    data-wow-delay="200ms"
                    data-wow-duration="1500ms"
                  >
                    <h3>
                      <FaUsers style={{ marginRight: '8px' }} />
                    </h3>
                    <p>Active Citizens</p>
                  </div>
                  <div
                    className="xis-testimonial-feature-item headline pera-content wow fadeFromUp"
                    data-wow-delay="400ms"
                    data-wow-duration="1500ms"
                  >
                    <h3>
                      <FaHandshake style={{ marginRight: '8px' }} />
                    </h3>
                    <p>Initiatives</p>
                  </div>
                  <div
                    className="xis-testimonial-feature-item headline pera-content wow fadeFromUp"
                    data-wow-delay="600ms"
                    data-wow-duration="1500ms"
                  >
                    <h3>
                      <FaLightbulb style={{ marginRight: '8px' }} />
                    </h3>
                    <p>Ideas Shared</p>
                  </div>
                </div>

                <div
                  className="it-nw-btn text-center wow flipInX mt-5"
                  data-wow-delay="200ms"
                  data-wow-duration="1500ms"
                >
                  <a
                    className="d-flex justify-content-center align-items-center tesitomonial-view-all-btn"
                    href="/competition-list"
                  >
                    Get Involved{" "}
                    <span className="arrow-box">
                      <FaArrowRight className="arrow-icon" />
                    </span>
                  </a>
                </div>
              </div>
            </div>

            <div className="col-lg-7">
              <div className="xis-testimonial-slider-wrapper">
                <Slider {...settings} className="xis-testimonial-slider">
                  {testimonials.map((item, i) => {
                    const baseClasses =
                      "xis-testi-slide-item d-flex align-items-center position-relative";
                    const slickClasses =
                      "slick-slide slick-current slick-active slick-center";

                    return (
                      <div
                        key={item.id}
                        className={
                          i === activeIndex
                            ? `${baseClasses} ${slickClasses}`
                            : baseClasses
                        }
                      >
                        <div className="xis-testi-img d-flex align-items-center justify-content-center" style={{ fontSize: '2.5rem', color: '#ff176b' }}>
                          {item.icon}
                        </div>
                        <div className="award xis-testi-text headline pera-content">
                          <h3>{item.title}</h3>
                          <small style={{ color: '#039', fontWeight: '600' }}>
                            {item.name}
                          </small>
                          <p className="d-flex align-items-center mt-2">
                            {item.description}
                          </p>
                        </div>
                      </div>
                    );
                  })}
                </Slider>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
};

export default WinnersSection;
