import { useState } from "react";
import { Link } from 'react-router-dom';

const DepartmentDetails = () => {
  const [activeTab, setActiveTab] = useState("all-c");

  const progressItems = [
    {
      img: "gh.png",
      title: "Chart showing decline in Infant Mortality Rate (IMR) in Kerala (2015-2023)",
    },
    {
      img: "tb.png",
      title: "Table showing infrastructure investment across key sectors ( In Crore) from 2015 to 2026",
    },
    {
      img: "ind.png",
      title: "Table showing infrastructure investment across key sectors ( In Crore) from 2015 to 2026",
    },  {
      img: "gh.png",
      title: "Chart showing decline in Infant Mortality Rate (IMR) in Kerala (2015-2023)",
    },
    {
      img: "tb.png",
      title: "Table showing infrastructure investment across key sectors ( In Crore) from 2015 to 2026",
    },
    {
      img: "ind.png",
      title: "Table showing infrastructure investment across key sectors ( In Crore) from 2015 to 2026",
    },
  ];

  const majorAchievements = [
    {
      img: "gh.png",
      title: "Achievement 1 description",
    },
    {
      img: "tb.png",
      title: "Achievement 2 description",
    },
    {
      img: "ind.png",
      title: "Achievement 3 description",
    },
  ];

  const awards = [
    { img: "award1.png", title: "Award 1" },
    { img: "award2.png", title: "Award 2" },
  ];

  const schemes = [
    { img: "scheme1.png", title: "Scheme 1" },
    { img: "scheme2.png", title: "Scheme 2" },
  ];

  const infra = [
    { img: "infra1.png", title: "Infrastructure 1" },
    { img: "infra2.png", title: "Infrastructure 2" },
  ];

  const renderCards = (items) =>
    items.map((item, index) => (
      <div className="col-md-4" key={index}>
        <Link to="/dept-detail" className="card chartt p-2">
          <img src={`/img/${item.img}`} className="w-100 mb-3" alt={item.title} />
          <div className="ctext">{item.title}</div>
          <div className="apldg-blog-meta1">
            <span className="apldg-blog-date">Read More</span>
          </div>
        </Link>
      </div>
    ));

  return (
    <>
      {/* Breadcrumb Section */}
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="container">
          <div className="breadcurmb-title">
            <h2>Insights</h2>
          </div>
          <div className="breadcurmb-item-list ul-li">
            <ul className="saasio-page-breadcurmb">
              <li><a href="#">Home</a></li>
              <li><a href="#">Discussion</a></li>
              <li><a href="#">Department Details</a></li>
            </ul>
          </div>
        </div>
      </section>

      {/* Department Details Section */}
      <section id="news-feed" className="news-feed-section position-relative">
        <div className="it-nw-side-bg text-center position-absolute">
          <img src="/design/assets/its-2/side-line.png" alt="" />
        </div>
        <div className="container">
          <div className="blog-feed-content">
            <div className="col-md-12 mx-auto">
              <div className="row">
                <div className="col-md-12">
                  <div className="deptdetail saasio-blog-details-content">
                    <div className="blog-details-text dia-headline wow fadeInTop mb-4 pb-3">
                      <h1 className="inhead Fword mt-2">
                        <span>Education</span> Department
                      </h1>
                    </div>

                    {/* Tabs */}
                    <div className="it-nw-about-tab-wrapper wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
                      <div className="it-nw-about-tab-btn">
                        <ul className="nav text-capitalize nav-tabs">
                          <li className="nav-item">
                            <a
                              className={`nav-link text-capitalize ${activeTab === "all-c" ? "active" : ""}`}
                              onClick={() => setActiveTab("all-c")}
                            >
                              Progress & Performance
                            </a>
                          </li>
                          <li className="nav-item">
                            <a
                              className={`nav-link text-capitalize ${activeTab === "my-c" ? "active" : ""}`}
                              onClick={() => setActiveTab("my-c")}
                            >
                              Major Achievements
                            </a>
                          </li>
                          <li className="nav-item">
                            <a
                              className={`nav-link text-capitalize ${activeTab === "award" ? "active" : ""}`}
                              onClick={() => setActiveTab("award")}
                            >
                              Awards & Accolades
                            </a>
                          </li>
                          <li className="nav-item">
                            <a
                              className={`nav-link text-capitalize ${activeTab === "scheme" ? "active" : ""}`}
                              onClick={() => setActiveTab("scheme")}
                            >
                              Schemes & Initiatives
                            </a>
                          </li>
                          <li className="nav-item">
                            <a
                              className={`nav-link text-capitalize ${activeTab === "infra" ? "active" : ""}`}
                              onClick={() => setActiveTab("infra")}
                            >
                              Infrastructure & Developments
                            </a>
                          </li>
                        </ul>
                      </div>

                      <div className="discuss ul-li-block pera-content mt-4">
                        <div className="tab-content">
                          {activeTab === "all-c" && (
                            <div className="tab-pane fade show active">
                              <div className="mb-40">
                                <h3 className="subN mb-30">
                                  <p>Progress & Performance</p>
                                  <a href="#">View All</a>
                                </h3>
                                <div className="row">{renderCards(progressItems)}</div>
                              </div>
                            </div>
                          )}

                          {activeTab === "my-c" && (
                            <div className="tab-pane fade show active">
                              <div className="mb-40">
                                <h3 className="subN mb-30">
                                  <p>Major Achievements</p>
                                  <a href="#">View All</a>
                                </h3>
                                <div className="row">{renderCards(majorAchievements)}</div>
                              </div>
                            </div>
                          )}

                          {activeTab === "award" && (
                            <div className="tab-pane fade show active">
                              <div className="mb-40">
                                <h3 className="subN mb-30">
                                  <p>Awards & Accolades</p>
                                  <a href="#">View All</a>
                                </h3>
                                <div className="row">{renderCards(awards)}</div>
                              </div>
                            </div>
                          )}

                          {activeTab === "scheme" && (
                            <div className="tab-pane fade show active">
                              <div className="mb-40">
                                <h3 className="subN mb-30">
                                  <p>Schemes & Initiatives</p>
                                  <a href="#">View All</a>
                                </h3>
                                <div className="row">{renderCards(schemes)}</div>
                              </div>
                            </div>
                          )}

                          {activeTab === "infra" && (
                            <div className="tab-pane fade show active">
                              <div className="mb-40">
                                <h3 className="subN mb-30">
                                  <p>Infrastructure & Developments</p>
                                  <a href="#">View All</a>
                                </h3>
                                <div className="row">{renderCards(infra)}</div>
                              </div>
                            </div>
                          )}
                        </div>
                      </div>
                    </div>

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

export default DepartmentDetails;
