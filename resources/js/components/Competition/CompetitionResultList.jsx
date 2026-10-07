import { useState } from "react";
import "./Competition.css";

export default function CompetitionResultList() {
  const [activeGroup, setActiveGroup] = useState(0);
  const [selectedVideo, setSelectedVideo] = useState(null);

  const data = [
    {
      id: 1,
      title: "First Prize",
      name: "Lorel Maddy",
      img: "/design/assets/thumb/author-1.jpg",
      video: "https://www.youtube.com/embed/8wenbhrsPn4",
      caption: "Winter is coming, as good the kings.",
    },
    {
      id: 2,
      title: "Second Prize",
      name: "John Doe",
      img: "/design/assets/thumb/author-2.jpg",
      video: "https://www.youtube.com/embed/VkQ6wzXfXqc",
      caption: "The art of resilience and courage.",
    },
    {
      id: 3,
      title: "Third Prize",
      name: "Sarah Lee",
      img: "/design/assets/thumb/author-3.jpg",
      video: "https://www.youtube.com/embed/kXYiU_JCYtU",
      caption: "Creativity beyond imagination.",
    },
  ];

   const shapes = [
        "Vector (1).svg",
        "Vector (2).svg",
        "Vector (5).svg",
        "Vector (3).svg",
        "Vector (4).svg",
    ];
    const shapeSizes = {
        "Vector (1).svg": 10,
        "Vector (2).svg": 0,
        "Vector (3).svg": 0,
        "Vector (4).svg": 16,
        "Vector (5).svg": 12,
    };

  return (
    <>
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="container-fluid">
          <div className="col-md-11 mx-auto">
            <div className="breadcurmb-title">
              <h2>Discussion</h2>
            </div>
            <div className="breadcurmb-item-list ul-li">
              <ul className="saasio-page-breadcurmb">
                <li>
                  <a href="#">Home</a>
                </li>
                <li>
                  <a href="#">Discussion</a>
                </li>
                <li>
                  <a href="#">Discussion Details</a>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      <section id="news-feed" className="news-feed-section-40 position-relative">
        {/* <div className="it-nw-side-bg-top text-center position-absolute">
          <img src="/design/assets/its-2/side-lines.png" alt="" />
        </div> */}
        {/* <div className="it-nw-side-bg-top text-center position-absolute">
          <img src="/design/assets/its-2/side-line.png" alt="" />
        </div> */}

        <div className="blog-shapes-grid-right">
                        {Array.from({ length: 100 }).map((_, i) => {
                            const file = shapes[i % shapes.length];
                            return (
                                <div
                                    className={`it-nw-blog-sh-bg sh${
                                        (i % 3) + 1
                                    }`}
                                    key={i}
                                >
                                    <img
                                        src={`/design/assets/bgv/${file}`}
                                        alt=""
                                        style={{
                                            width: shapeSizes[file] + "px",
                                        }}
                                    />
                                    {/* <img
                                        src={`/design/assets/bgv/Vector (2).svg`}
                                        alt=""
                                        style={{
                                            width: "30px",
                                        }}
                                    />  */}
                                </div>
                            );
                        })}
                    </div>
        <div className="container-fluid">
          <div className="col-md-9 mx-auto blog-feed-content">
            <div className="row">
              {/* Main Content */}
              <div className="col-md-8">
                <div className="saasio-blog-details-content">
                  <div className="blog-details-text dia-headline">
                    <h2 className="competition-title">
                      Sample Competition Activity Name
                    </h2>

                    {/* Banner & Info Row */}
                    <div className="competition-header">
                      <div className="competition-banner">
                        <img
                          src="/design/assets/fp/5_ഹരിതകർമസേന ക്വിസ്.jpg"
                          alt="Competition Banner"
                        />
                      </div>
                      <div className="competition-info">
                        <div className="info-center">
                          <p>
                            Start date : <span>20 Friday 2025</span>
                          </p>
                          <p>
                            End date : <span>25 Friday 2025</span>
                          </p>

                          <div className="competition-stats">
                            <div>
                              <strong>202</strong>
                              <p>Entries</p>
                            </div>
                            <div className="divider"></div>
                            <div>
                              <strong>50</strong>
                              <p>Rejected</p>
                            </div>
                            <div className="divider"></div>
                            <div>
                              <strong>45</strong>
                              <p>Approved</p>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    {/* Winners Section */}
                    {/* <div className="winner-section">
                      {data.map((item, index) => (
                        <div className="winner-card" key={item.id}>
                          <div className="winner-header">
                            <img className="profile-pic" src={item.img} alt={item.name} />
                            <div>
                              <h4>{item.name}</h4>
                              <p>{item.title}</p>
                            </div>
                            {index === 0 && <span className="ribbon">🥇</span>}
                            {index === 1 && <span className="ribbon silver">🥈</span>}
                            {index === 2 && <span className="ribbon bronze">🥉</span>}
                          </div>
                          <div className="winner-media">
                            <iframe
                              width="100%"
                              height="200"
                              src={item.video}
                              title={item.title}
                              frameBorder="0"
                              allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                              allowFullScreen
                            ></iframe>
                          </div>
                          <p className="caption">{item.caption}</p>
                        </div>
                      ))}
                    </div> */}

                    {/* <div className="winner-section">
                      {data.map((item, index) => (
                        <div className="winner-card" key={item.id}>
                          <div className="winner-header">
                            <img
                              className="profile-pic"
                              src={item.img}
                              alt={item.name}
                            />
                            <div>
                              <h4>{item.name}</h4>
                              <p>{item.title}</p>
                            </div>
                            {index === 0 && <span className="ribbon">🏅</span>}
                          </div>
                          <div
                            className="winner-media"
                            onClick={() => setSelectedVideo(item.video)}
                          >
                            <img
                              src={`https://img.youtube.com/vi/${
                                item.video.split("/embed/")[1]
                              }/hqdefault.jpg`}
                              alt="video thumbnail"
                              className="video-thumb"
                            />
                            <div className="play-icon">
                              <i className="fas fa-play"></i>
                            </div>
                          </div>
                          <p className="caption">{item.caption}</p>
                        </div>
                      ))}
                    </div> */}
                    <div className="winner-section row">
                      {data.map((item, index) => (
                        <div
                          className="col-md-4 col-sm-6 col-12 mb-4"
                          key={item.id}
                        >
                          <div className="winner-card">
                            <div className="winner-header">
                              <img
                                className="profile-pic"
                                src={item.img}
                                alt={item.name}
                              />
                              <div>
                                <h4>{item.name}</h4>
                                <p>{item.title}</p>
                              </div>
                              {index === 0 && (
  <img src="/design/assets/competition/badge-1.svg" alt="Ribbon" className="ribbon" />
                              )} {index === 1 && (
  <img src="/design/assets/competition/badge-2.svg" alt="Ribbon" className="ribbon" />
                              )}{index === 2 && (
  <img src="/design/assets/competition/badge-3.svg" alt="Ribbon" className="ribbon" />
                              )}
                            </div>

                            <div
                              className="winner-media"
                              onClick={() => setSelectedVideo(item.video)}
                            >
                              <img
                                src={`https://img.youtube.com/vi/${
                                  item.video.split("/embed/")[1]
                                }/hqdefault.jpg`}
                                alt="video thumbnail"
                                className="video-thumb"
                              />
                              <div className="play-icon">
                                <i className="fas fa-play"></i>
                              </div>
                            </div>

                            <p className="caption">{item.caption}</p>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                </div>
              </div>

              {/* Sidebar */}
              <div className="col-md-4">
                <div className="saasio-blog-sidebar">
                  <div
                    className="side-bar-widget wow fadeInRight"
                    style={{ backgroundColor: "#12111a" }}
                  >
                    <div className="search-widget dia-headline">
                      <form action="" className="relative-position">
                        <input
                          type="text"
                          name="search"
                          placeholder="Search Here"
                          style={{ backgroundColor: "#f9f9f9" }}
                        />
                        <button type="submit">
                          <i className="fas fa-search"></i>
                        </button>
                      </form>
                    </div>
                  </div>
                  {/* Video Modal */}
                  {selectedVideo && (
                    <div
                      className="video-modal"
                      onClick={() => setSelectedVideo(null)}
                    >
                      <div
                        className="video-container"
                        onClick={(e) => e.stopPropagation()}
                      >
                        <iframe
                          src={selectedVideo}
                          title="YouTube Video"
                          frameBorder="0"
                          allowFullScreen
                        ></iframe>
                        <button
                          className="close-btn"
                          onClick={() => setSelectedVideo(null)}
                        >
                          ✕
                        </button>
                      </div>
                    </div>
                  )}
                  <div className="side-bar-widget wow fadeInUp">
                    <div className="category-widget dia-headline ul-li-block">
                      <h3 className="widget-title-2">Related Articles</h3>
                      <div className="recent-post-area">
                        {[
                          "Discussion about current trajedies of political parties in india",
                          "Engaging New Smart Approach.",
                          "Engaging New Smart Approach.",
                          "Engaging New Smart Approach.",
                        ].map((text, idx) => (
                          <div className="recent-post-img-text" key={idx}>
                            <a href="#">
                              <div className="recent-post-img float-left">
                                <img src="/design/assets/dis.jpg" alt="Related Post" />
                              </div>
                              <div className="recent-post-text dia-headline">
                                <h3>
                                  <a href="#">{text}</a>
                                </h3>
                                <span className="rec-post-meta">
                                  <a href="#">
                                    Last Date:{" "}
                                    <span className="day">
                                      December 12, 2021
                                    </span>
                                  </a>
                                </span>
                              </div>
                            </a>
                          </div>
                        ))}
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
}