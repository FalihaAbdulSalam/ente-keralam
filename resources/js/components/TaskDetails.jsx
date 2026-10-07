"use client";
import { useState } from "react";

const TaskDetails = () => {
  const [showDetails, setShowDetails] = useState(true);
  const [activeTab, setActiveTab] = useState("all");
  const [expanded, setExpanded] = useState(false);
  const [fileName, setFileName] = useState("Add Image/PDF");

  const handleFileChange = (e) => {
    if (e.target.files.length > 0) {
      setFileName(e.target.files[0].name);
    } else {
      setFileName("Add Image/PDF");
    }
  };

  return (
    <><section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
      {/* <div className="container-fluid ">
        <div className="breadcurmb-title">
          <h2>Discussion</h2>
        </div>
        <div className="breadcurmb-item-list ul-li">
          <ul className="saasio-page-breadcurmb">
            <li><a href="#">Home</a></li>
            <li><a href="#">Discussion</a></li>
            <li><a href="#">Discussion Details</a></li>
          </ul>
        </div>
      </div> */}
      <div className="container-fluid">
			 <div className="col-md-11 mx-auto">
				 <div className="breadcurmb-title ">
				<h2>Discussion</h2>
			  </div>
			  <div className="breadcurmb-item-list ul-li">
				<ul className="saasio-page-breadcurmb">
				  <li><a href="#">Home</a></li>
				  <li><a href="#">Discussion</a></li>
				  <li><a href="#">Discussion Details</a></li>
				</ul>
			  </div>
			 </div>
			</div>
    </section>
    <section id="news-feed" className="news-feed-section">
        <div className="container">
          <div className="blog-feed-content pollDet">
            <div className="row">
              <div className="">
                <div className="saasio-blog-details-content">
                  <div className="blog-details-text dia-headline">
                    <h2>Content without backward-compatible data.</h2>

                    <div className="d-flex justify-content-between">
                      {/* Dates */}
                      <div className="saasio-post-meta dates">
                        <a href="#" className="date">
                          <svg
                            className="svg-css"
                            id="Capa_1"
                            enableBackground="new 0 0 512.228 512.228"
                            height="13"
                            viewBox="0 0 512.228 512.228"
                            width="13"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <g>
                              <path d="m413.333 39.447h-19.106v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-196.227v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-19.105c-54.531 0-98.895 44.364-98.895 98.894v274.878c0 54.531 44.364 98.895 98.895 98.895h314.439c54.53 0 98.894-44.364 98.894-98.895v-274.878c0-54.53-44.364-98.894-98.895-98.894zm-314.438 40h19.105v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h196.228v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h19.106c32.474 0 58.894 26.42 58.894 58.894v19.106h-432.228v-19.106c0-32.474 26.42-58.894 58.895-58.894zm314.438 392.667h-314.438c-32.475 0-58.895-26.42-58.895-58.895v-215.772h432.228v215.772c0 32.475-26.42 58.895-58.895 58.895zm-235.666-196c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118 118c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20z" />
                            </g>
                          </svg>
                          Start Date: <span>September 12, 2021</span>
                          </a>
                        <a href="#"  className="date">
                          <svg
                            className="svg-css"
                            id="Capa_1"
                            enableBackground="new 0 0 512.228 512.228"
                            height="13"
                            viewBox="0 0 512.228 512.228"
                            width="13"
                            xmlns="http://www.w3.org/2000/svg"
                          >
                            <g>
                              <path d="m413.333 39.447h-19.106v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-196.227v-19.333c0-11.046-8.954-20-20-20s-20 8.954-20 20v19.333h-19.105c-54.531 0-98.895 44.364-98.895 98.894v274.878c0 54.531 44.364 98.895 98.895 98.895h314.439c54.53 0 98.894-44.364 98.894-98.895v-274.878c0-54.53-44.364-98.894-98.895-98.894zm-314.438 40h19.105v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h196.228v39c0 11.046 8.954 20 20 20s20-8.954 20-20v-39h19.106c32.474 0 58.894 26.42 58.894 58.894v19.106h-432.228v-19.106c0-32.474 26.42-58.894 58.895-58.894zm314.438 392.667h-314.438c-32.475 0-58.895-26.42-58.895-58.895v-215.772h432.228v215.772c0 32.475-26.42 58.895-58.895 58.895zm-235.666-196c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118 118c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm236.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20zm-118.228 0c0 11.046-8.954 20-20 20h-39.333c-11.046 0-20-8.954-20-20s8.954-20 20-20h39.333c11.045 0 20 8.954 20 20z" />
                            </g>
                          </svg>
                          End Date: <span>September 12, 2021</span>
                        </a>
                      </div>

                      {/* Share */}
                      <div className="blog-feed-share float-right">
                        <small>Share:</small>
                        <a href="#">
                          <img src="/design/assets/social/facebook.svg" width="22" alt="" />
                        </a>
                        <a href="#">
                          <img src="/design/assets/social/insta.svg" width="22" alt="" />
                        </a>
                        <a href="#">
                          <img src="/design/assets/social/whatsapp.svg" width="22" alt="" />
                        </a>
                        <a href="#">
                          <img src="/design/assets/social/twitter.svg" width="22" alt="" />
                        </a>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Poster */}
                <div className="blog-details-img wow fadeInLeft" data-wow-delay="200ms" data-wow-duration="1500ms">
                  <div className="postr">
                    <div className="d-flex">
                      <div className="col-lg-7 p-0">
                        <img src="/design/assets/dd.png" alt="" />
                      </div>
                      <div className="col-lg-5 p-0 d-flex align-items-center justify-content-center">
                        <div className="it-nw-btn text-center wow fadeInRight" data-wow-delay="200ms" data-wow-duration="1500ms">
                          <a
                            className="d-flex part justify-content-center align-items-center"
                            href="#"
                          >
                            Login to participate
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Articles */}
                {showDetails && (
                  <div className="blog-details-text dia-headline wow fadeInRight">
                    <article>
                      Har Ghar Tiranga is a campaign that encourages people to
                      bring Tiranga home and hoist it to mark India's
                      independence.
                    </article>
                    <article>
                      The point of using Lorem Ipsum The man, who is in a stable
                      condition in hospital, has "potentially life-changing
                      injuries" after the overnight attack in Garvagh, County Lono
                      donderry. He was shot in the arms and legs."What sort of men
                      would think it is acceptable to subject a young girl to this
                      level of brutality and violence?
                    </article>
                  </div>
                )}

                {/* Hide/Show details */}
                <div className="mt-2">
                  <a
                    className="hidex"
                    onClick={() => setShowDetails((prev) => !prev)}
                  >
                    <img
                      src="/design/assets/alignment.png"
                      width="20"
                      alt=""
                      className="mr-2" />
                    {showDetails ? "Hide Details" : "Show Details"}
                  </a>
                </div>

                {/* Task Counts */}
                {/* <div className="card p-2 mt-4 taskcount">
                  <div className="row align-items-center">
                    <div className="col-12 col-md-3 text-center px-3">
                      <h6>SUBMISSIONS UNDER THIS TASK</h6>
                    </div>
                    <div className="col-4 col-md-3 text-center line">
                      <h5 style={{ color: "#00133e" }}>828</h5>
                      <p>Total</p>
                    </div>
                    <div className="col-4 col-md-3 text-center">
                      <h5 style={{ color: "green" }}>1</h5>
                      <p>Approved</p>
                    </div>
                    <div className="col-4 col-md-3 text-center">
                      <h5 style={{ color: "#891b1b" }}>630</h5>
                      <p>Under Review</p>
                    </div>
                  </div>
                </div> */}

                {/* Comments */}
                <div className="it-nw-about-tab-wrapper wow fadeInUp" data-wow-delay="200ms" data-wow-duration="1500ms">
                  {/* Tabs */}
                  <div className="it-nw-about-tab-btn">
                    <ul id="tabs" className="nav text-capitalize nav-tabs">
                      <li className="nav-item">
                        <button
                          className={`nav-link text-capitalize ${activeTab === "all" ? "active show" : ""}`}
                          onClick={() => setActiveTab("all")}
                        >
                          All Comments
                        </button>
                      </li>
                      <li className="nav-item">
                        <button
                          className={`nav-link text-capitalize ${activeTab === "my" ? "active show" : ""}`}
                          onClick={() => setActiveTab("my")}
                        >
                          My Comments
                        </button>
                      </li>
                    </ul>
                  </div>

                  {/* Comment Box */}
                  <div className="my-text1 d-flex">
                    <img src="/design/assets/prof.jpg" alt="" />
                    <div className="w-100">
                      {!expanded ? (
                        <input
                          type="text"
                          className="form-control form-control-lg"
                          placeholder="Write your Opinion"
                          onClick={() => setExpanded(true)} />
                      ) : (
                        <div
                          className="text-era"
                          id="text-era"
                          style={{ display: "block" }}
                        >
                          <textarea
                            className="w-100"
                            placeholder="Share Your Views..."
                            rows="4"
                            spellCheck="false"
                          ></textarea>

                          <input
                            type="file"
                            id="actual-btn"
                            hidden
                            onChange={handleFileChange} />
                          <label htmlFor="actual-btn">Choose File</label>
                          <span id="file-chosen">{fileName}</span>

                          <div className="it-nw-btn text-center mt-2">
                            <button className="d-flex part justify-content-center align-items-center">
                              Submit
                            </button>
                          </div>
                        </div>
                      )}
                    </div>
                  </div>

                  {/* Comment List */}
                  <div className="discuss ul-li-block pera-content">
                    <div id="tabsContent" className="tab-content">
                      {activeTab === "all" && (
                        <div id="all-c" className="tab-pane fade active show">
                          <ul>
                            <li className="parent">
                              <div className="pro-sec">
                                <img src="/design/assets/comment/team-2.jpg" alt="" />
                                <div>
                                  <h4>Angoor Ravuthar</h4>
                                  <span>12 hours 29 minutes ago</span>
                                </div>
                              </div>
                              <p>
                                Lorem ipsum dolor sit amet consectetur adipisicing
                                elit. Cum dolores dolore sed expedita, recusandae
                                repellendus aperiam maxime aliquid voluptas dicta
                                necessitatibus quidem doloribus excepturi
                                molestias illo quas aspernatur iste repellat.
                              </p>
                            </li>
                          </ul>
                        </div>
                      )}

                      {activeTab === "my" && (
                        <div id="my-c" className="tab-pane fade active show">
                          rrrrrrr
                        </div>
                      )}
                    </div>
                  </div>
                </div>
                {/* End comments */}
              </div>
            </div>
          </div>
        </div>
      </section></>
  );
};

export default TaskDetails;
