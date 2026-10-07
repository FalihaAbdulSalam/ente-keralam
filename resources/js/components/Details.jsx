"use client";
import { useState } from "react";
import {
  FaUser,
  FaCalendarAlt,
  FaSearch,
  FaFacebookF,
  FaInstagram,
  FaTwitter,
} from "react-icons/fa";
import { FaWhatsapp } from "react-icons/fa6";
import Slider from "react-slick";
import "slick-carousel/slick/slick.css";
import "slick-carousel/slick/slick-theme.css";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
import { useLanguage } from "./LanguageContext";

const NewsFeed = () => {
  const { language } = useLanguage();
  const [activeTab, setActiveTab] = useState("all");
  const [expanded, setExpanded] = useState(false);
  const [fileName, setFileName] = useState("Add Image/PDF");
  const [activeGroup, setActiveGroup] = useState(0);

  // Helper to get display text based on language
  const getDisplayText = (enText, malText) => {
    return language === 'ml' ? (malText || enText) : enText;
  };

  const data = [
    { id: 1, title: "Sample Heading", img: "/img/newsletter/2.webp" },
    { id: 2, title: "Sample Heading", img: "/img/newsletter/2.webp" },
    { id: 3, title: "Sample Heading", img: "/img/newsletter/2.webp" },
    { id: 4, title: "Sample Heading", img: "/img/newsletter/2.webp" },
    { id: 5, title: "Sample Heading", img: "/img/newsletter/2.webp" },
    { id: 6, title: "Sample Heading", img: "/img/newsletter/2.webp" },
  ];

  const totalGroups = Math.ceil(data.length / 3);

  const handleFileChange = (e) => {
    if (e.target.files.length > 0) {
      setFileName(e.target.files[0].name);
    } else {
      setFileName("Add Image/PDF");
    }
  };

  // const settings = {
  //   dots: true,
  //   arrows: false,
  //   infinite: true,
  //   autoplay: true,
  //   autoplaySpeed: 3000,
  //   speed: 800,
  //   slidesToShow: 2, // show 2 at once
  //   slidesToScroll: 1,
  //   responsive: [
  //     {
  //       breakpoint: 768, // for mobile
  //       settings: { slidesToShow: 1 },
  //     },
  //   ],
  //   appendDots: (dots) => (
  //     <div
  //       style={{
  //         bottom: "-15px",
  //       }}
  //     >
  //       <ul style={{ margin: "0px" }}> {dots} </ul>
  //     </div>
  //   ),
  //   customPaging: (i) => (
  //     <div
  //       style={{
  //         width: "10px",
  //         height: "10px",
  //         borderRadius: "50%",
  //         background: "#d1d5db", // light gray for inactive
  //       }}
  //     ></div>
  //   ),
  // };

  const discussions = [
    {
      id: 1,
      title: "Discussion about current tragedies of political parties in India",
      date: "December 12, 2021",
      image: "/img/dis.jpg",
      link: "#",
    },
    {
      id: 2,
      title: "Engaging New Smart Approach.",
      date: "December 12, 2021",
      image: "/img/dis.jpg",
      link: "#",
    },
    {
      id: 3,
      title: "Engaging New Smart Approach.",
      date: "December 12, 2021",
      image: "/img/dis.jpg",
      link: "#",
    },
  ];

  // const newsletters = [
  //   { id: 1, image: "/img/newsletter/2.webp" },
  //   { id: 2, image: "/img/newsletter/2.webp" },
  //   { id: 3, image: "/img/newsletter/2.webp" },
  //   { id: 4, image: "/img/newsletter/2.webp" },
  // ];

  return (
    <>
      <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
        <div className="container">
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
      </section>
      <section id="news-feed" className="news-feed-section">
        <div className="container">
          <div className="blog-feed-content">
            <div className="row">
              {/* Left Content */}
              <div className="col-md-8">
                <div className="saasio-blog-details-content">
                  <div className="blog-details-text dia-headline wow fadeInTop">
                    <h2>Content without backward-compatible data.</h2>

                    {/* Post Meta */}
                    <div className="saasio-post-meta">
                      <a href="#">
                        <svg
                          id="Layer_1"
                          className="svg-css"
                          width="13"
                          height="13"
                          enableBackground="new 0 0 512 512"
                          viewBox="0 0 512 512"
                          xmlns="http://www.w3.org/2000/svg"
                        >
                          <g id="Layer_2_00000152227627889381600950000007803127182259535500_">
                            <g id="Layer_1_copy_2">
                              <g id="_108">
                                <path d="m334.8 314.4h-157.6c-97.7 0-177.2 79.5-177.2 177.2 0 11 9 20 20 20h472c11 0 20-9 20-20 0-97.7-79.5-177.2-177.2-177.2zm-293.3 157.2c9.7-66.2 66.9-117.2 135.8-117.2h157.5c68.9 0 126.1 51 135.8 117.2z" />
                                <path d="m256 279.4c76.9 0 139.5-62.6 139.5-139.5s-62.6-139.5-139.5-139.5-139.5 62.6-139.5 139.5 62.6 139.5 139.5 139.5zm0-239c55 0 99.5 44.5 99.5 99.5s-44.5 99.5-99.5 99.5-99.5-44.5-99.5-99.5c.1-54.9 44.6-99.4 99.5-99.5z" />
                              </g>
                            </g>
                          </g>
                        </svg>
                        <span>Fisheries Department</span>
                      </a>
                      <a href="#">
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
                        </svg>{" "}
                        Start : <span>September 12, 2021</span>
                      </a>
                      <a href="#">
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
                        </svg>{" "}
                        End : <span>September 12, 2021</span>
                      </a>
                    </div>
                  </div>

                  {/* Blog Image */}
                  <div
                    className="blog-details-img wow fadeInLeft"
                    data-wow-delay="200ms"
                    data-wow-duration="1500ms"
                  >
                    <img src="/design/assets/dd.png" alt="" />
                    <div className="overlayz">
                      <div
                        className="it-nw-btn text-center wow fadeInRight"
                        data-wow-delay="200ms"
                        data-wow-duration="1500ms"
                      >
                        <a
                          className="d-flex part justify-content-center align-items-center"
                          href="#"
                        >
                          Login to participate
                        </a>
                      </div>
                    </div>
                  </div>

                  {/* Blog Text */}
                  <div className="blog-details-text dia-headline wow fadeInRight">
                    <article>
                      Har Ghar Tiranga is a campaign that encourages people to
                      bring Tiranga home and hoist it to mark India's
                      independence.
                    </article>
                    <article>
                      The point of using Lorem Ipsum The man, who is in a stable
                      condition in hospital, has "potentially life-changing
                      injuries" after the overnight attack in Garvagh, County
                      Lono donderry. He was shot in the arms and legs."What sort
                      of men would think it is accepttable to sub ject a young
                      girl to this level of brutality and violence?
                    </article>
                    <article>
                      It is a long established fact that a reader will be
                      distracted by the readable content of a page when looking
                      at its layout. The point of using Lorem Ipsum The man, who
                      is in a stable condition in hospital, has "potentially
                      life-changing injuries" after the overnight attack in
                      Garvagh, County Lono donderry. He was shot in the arms and
                      legs."What sort of men would think it is accepttable to
                      sub ject a young girl to this level of brutality and
                      violence?
                    </article>
                  </div>

                  {/* Hide / Share */}
                  <div className="mt-4">
                    <a href="#" className="hidex">
                      <img
                        src="/design/assets/alignment.png"
                        width="20"
                        alt=""
                        className="mr-2"
                      />
                      Hide Details
                    </a>
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

                  <hr />

                  {/* Comments */}
                  <div
                    className="it-nw-about-tab-wrapper wow fadeInUp"
                    data-wow-delay="200ms"
                    data-wow-duration="1500ms"
                  >
                    {/* Tabs */}
                    <div className="it-nw-about-tab-btn">
                      <ul id="tabs" className="nav text-capitalize nav-tabs">
                        <li className="nav-item">
                          <a
                            className={`nav-link text-capitalize ${
                              activeTab === "all" ? "active show" : ""
                            }`}
                            onClick={() => setActiveTab("all")}
                          >
                            All Comments
                          </a>
                        </li>
                        <li className="nav-item">
                          <a
                            className={`nav-link text-capitalize ${
                              activeTab === "my" ? "active show" : ""
                            }`}
                            onClick={() => setActiveTab("my")}
                          >
                            My Comments
                          </a>
                        </li>
                      </ul>
                    </div>

                    {/* Comment Input */}
                    <div className="my-text1">
                      <img src="/design/assets/prof.jpg" alt="profile" />
                      <div className="w-100">
                        {!expanded ? (
                          <input
                            type="text"
                            className="form-control form-control-lg"
                            id="input-field"
                            placeholder="Write your Opinion"
                            require
                            onFocus={() => setExpanded(true)}
                          />
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

                            {/* File Upload */}
                            <input
                              type="file"
                              id="actual-btn"
                              hidden
                              onChange={handleFileChange}
                            />
                            <label htmlFor="actual-btn" className="file-label">
                              Choose File
                            </label>

                            <span className="ms-1" id="file-chosen">
                              {fileName}
                            </span>

                            {/* Submit */}
                            <div className="it-nw-btn text-center">
                              <a
                                className="d-flex part justify-content-center align-items-center"
                                href="#"
                              >
                                Submit
                              </a>
                            </div>
                          </div>
                        )}
                      </div>
                    </div>

                    {/* Comments List */}
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
                                  Lorem ipsum dolor sit amet consectetur
                                  adipisicing elit. Cum dolores dolore sed
                                  expedita, recusandae repellendus aperiam
                                  maxime aliquid voluptas dicta necessitatibus
                                  quidem doloribus excepturi molestias illo quas
                                  aspernatur iste repellat. Lorem ipsum dolor
                                  sit amet consectetur adipisicing elit. Cum
                                  dolores dolore sed expedita, recusandae
                                  repellendus aperiam maxime aliquid voluptas
                                  dicta necessitatibus quidem doloribus
                                  excepturi molestias illo quas aspernatur iste
                                  repellat.
                                </p>
                                <div className="action-sec"></div>
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
                </div>
              </div>

              {/* Sidebar */}
              <div className="col-md-4">
                <div className="saasio-blog-sidebar">
                  {/* Search */}
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
                          <FaSearch />
                        </button>
                      </form>
                    </div>
                  </div>

                  {/* Categories */}
                  <div
                    className="side-bar-widget wow fadeInUp"
                    data-wow-delay="200ms"
                    data-wow-duration="1500ms"
                  >
                    <div className="category-widget dia-headline ul-li-block">
                      <h3 className="widget-title-2">Category</h3>
                      <ul className="taksx">
                        <li>
                          <a href="#">
                            <svg
                              id="Capa_1"
                              enableBackground="new 0 0 512 512"
                              height="20"
                              viewBox="0 0 512 512"
                              width="20"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path d="m434.929 46.131c-10.38-10.402-24.184-16.131-38.871-16.131h-24.808v-5c0-13.785-11.215-25-25-25h-180c-13.785 0-25 11.215-25 25v5h-24.897c-30.261 0-54.908 24.646-54.942 54.939l-.411 372c-.016 14.702 5.691 28.528 16.07 38.93 10.38 10.402 24.185 16.131 38.872 16.131h279.704c30.262 0 54.909-24.646 54.942-54.939l.412-372c.017-14.703-5.691-28.529-16.071-38.93zm-263.679-16.131h170v30h-170zm249.37 427.027c-.016 13.771-11.219 24.973-24.974 24.973h-279.704c-6.676 0-12.951-2.604-17.669-7.332-4.718-4.729-7.312-11.013-7.305-17.695l.411-372c.015-13.771 11.218-24.973 24.974-24.973h24.897v5c0 13.785 11.215 25 25 25h180c13.785 0 25-11.215 25-25v-5h24.808c6.676 0 12.951 2.604 17.669 7.332s7.313 11.013 7.305 17.695z" />
                              <path d="m261.099 200h106.571c8.284 0 15-6.716 15-15s-6.716-15-15-15h-106.571c-8.284 0-15 6.716-15 15s6.716 15 15 15z" />
                              <path d="m261.099 300h106.571c8.284 0 15-6.716 15-15s-6.716-15-15-15h-106.571c-8.284 0-15 6.716-15 15s6.716 15 15 15z" />
                              <path d="m368.099 370h-107c-8.284 0-15 6.716-15 15s6.716 15 15 15h107c8.284 0 15-6.716 15-15s-6.715-15-15-15z" />
                              <path d="m197.256 144.157-34.592 34.592-8.156-8.157c-5.858-5.858-15.355-5.858-21.213 0-5.858 5.857-5.858 15.355 0 21.213l18.763 18.764c2.813 2.813 6.628 4.394 10.607 4.394 3.978 0 7.793-1.58 10.606-4.394l45.199-45.198c5.858-5.857 5.858-15.355 0-21.213-5.858-5.859-15.355-5.859-21.214-.001z" />
                              <path d="m197.256 251.794-34.592 34.592-8.156-8.156c-5.858-5.858-15.355-5.858-21.213 0-5.858 5.857-5.858 15.354 0 21.213l18.763 18.764c2.813 2.813 6.628 4.394 10.607 4.394 3.978 0 7.794-1.58 10.606-4.394l45.199-45.199c5.858-5.857 5.858-15.355 0-21.213s-15.356-5.858-21.214-.001z" />
                              <path d="m197.256 351.794-34.592 34.592-8.156-8.156c-5.858-5.858-15.355-5.858-21.213 0-5.858 5.857-5.858 15.354 0 21.213l18.763 18.764c2.813 2.813 6.628 4.394 10.607 4.394 3.978 0 7.794-1.58 10.606-4.394l45.199-45.199c5.858-5.857 5.858-15.355 0-21.213s-15.356-5.858-21.214-.001z" />
                            </svg>
                            To Do <span>5</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <svg
                              height="20"
                              viewBox="0 0 512 512"
                              width="20"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path d="m136 126c5.519531 0 10-4.480469 10-10s-4.480469-10-10-10-10 4.480469-10 10 4.480469 10 10 10zm0 0" />
                              <path d="m136 166c5.519531 0 10-4.480469 10-10s-4.480469-10-10-10-10 4.480469-10 10 4.480469 10 10 10zm0 0" />
                              <path d="m10 512h180c5.523438 0 10-4.476562 10-10v-10c0-42.785156-26.648438-78.445312-62.882812-92.910156 13.921874-10.996094 22.882812-28.011719 22.882812-47.089844 0-33.085938-26.914062-60-60-60s-60 26.914062-60 60c0 19.101562 8.980469 36.128906 22.929688 47.128906-36.839844 14.753906-62.929688 50.820313-62.929688 92.871094v10c0 5.523438 4.476562 10 10 10zm50-160c0-22.054688 17.945312-40 40-40s40 17.945312 40 40-17.945312 40-40 40-40-17.945312-40-40zm40 60c44.207031 0 80 35.757812 80 80h-160c0-44.113281 35.886719-80 80-80zm0 0" />
                              <path d="m512 502v-10c0-42.785156-26.648438-78.445312-62.882812-92.910156 13.921874-10.996094 22.882812-28.011719 22.882812-47.089844 0-33.085938-26.914062-60-60-60s-60 26.914062-60 60c0 19.101562 8.980469 36.128906 22.929688 47.128906-36.839844 14.753906-62.929688 50.820313-62.929688 92.871094v10c0 5.523438 4.476562 10 10 10h180c5.523438 0 10-4.476562 10-10zm-140-150c0-22.054688 17.945312-40 40-40s40 17.945312 40 40-17.945312 40-40 40-40-17.945312-40-40zm-40 140c0-44.113281 35.886719-80 80-80 44.207031 0 80 35.757812 80 80zm0 0" />
                              <path d="m111.558594 271.339844c3.371094 1.671875 7.441406 1.320312 10.496094-1l58.316406-44.339844h155.628906c5.523438 0 10-4.476562 10-10v-30h7.109375l57.371094 37.960938c3.078125 2.03125 7.015625 2.210937 10.253906.46875 3.242187-1.742188 5.265625-5.128907 5.265625-8.808594v-30.621094h29c5.523438 0 10-4.476562 10-10v-165c0-5.523438-4.476562-10-10-10h-260c-5.523438 0-10 4.476562-10 10v36h-109c-5.523438 0-10 4.476562-10 10v161c0 5.523438 4.476562 10 10 10h30v35.378906c0 3.800782 2.152344 7.269532 5.558594 8.960938zm93.441406-251.339844h240v145h-29c-5.523438 0-10 4.476562-10 10v22.011719l-44.363281-29.351563c-1.636719-1.082031-3.554688-1.660156-5.515625-1.660156h-10.121094v-41h49.941406c5.523438 0 10-4.476562 10-10s-4.476562-10-10-10h-49.941406v-20h49.820312c5.523438 0 10-4.476562 10-10s-4.476562-10-10-10h-51.890624c-4.386719-11.183594-15.3125-19-28.070313-19-.03125 0-.0625 0-.089844 0h-110.769531zm-119 187v-141h229.796875.03125c5.589844 0 9.992187 4.628906 9.992187 9 0 .636719.066407 1.261719.179688 1.863281v129.136719h-149c-2.1875 0-4.3125.71875-6.054688 2.039062l-44.945312 34.175782v-25.214844c0-5.523438-4.476562-10-10-10zm0 0" />
                              <path d="m176 126h100c5.523438 0 10-4.476562 10-10s-4.476562-10-10-10h-100c-5.523438 0-10 4.476562-10 10s4.476562 10 10 10zm0 0" />
                              <path d="m176 166h100c5.523438 0 10-4.476562 10-10s-4.476562-10-10-10h-100c-5.523438 0-10 4.476562-10 10s4.476562 10 10 10zm0 0" />
                            </svg>
                            Discussion <span>2</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <svg
                              clip-rule="evenodd"
                              height="20"
                              width="20"
                              fill-rule="evenodd"
                              stroke-linejoin="round"
                              stroke-miterlimit="2"
                              viewBox="0 0 2134 2134"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path d="m1967.57 1789.47c44.853 32.045 74.097 84.535 74.097 143.86 0 23.012-18.655 41.667-41.667 41.667h-270.072c-23.012 0-41.667-18.655-41.667-41.667 0-59.325 29.244-111.815 74.097-143.86-7.446-15.165-11.628-32.218-11.628-50.239 0-63.048 51.187-114.234 114.234-114.234s114.234 51.186 114.234 114.234c0 18.021-4.182 35.074-11.628 50.239zm-139.301 57.964c-20.384 8.715-37.032 24.481-46.89 44.23 0 0 167.17 0 167.171.001-9.858-19.749-26.507-35.515-46.891-44.231-11.519 3.91-23.861 6.031-36.695 6.031s-25.176-2.121-36.695-6.031zm36.695-139.104c-17.054 0-30.9 13.846-30.9 30.901 0 17.054 13.846 30.9 30.9 30.9s30.901-13.846 30.901-30.9c0-17.055-13.847-30.901-30.901-30.901zm-430.728 81.14c44.854 32.045 74.097 84.535 74.097 143.86 0 23.012-18.654 41.667-41.666 41.667h-270.072c-23.012 0-41.667-18.655-41.667-41.667 0-59.325 29.244-111.815 74.097-143.86-7.446-15.165-11.628-32.218-11.628-50.239 0-63.048 51.186-114.234 114.234-114.234 63.047 0 114.234 51.186 114.234 114.234 0 18.021-4.183 35.074-11.629 50.239zm-102.605-81.14c-17.055 0-30.901 13.846-30.901 30.901 0 17.054 13.846 30.9 30.901 30.9 17.054 0 30.9-13.846 30.9-30.9 0-17.055-13.846-30.901-30.9-30.901zm-36.695 139.104c-20.384 8.715-37.033 24.481-46.891 44.23 0 0 167.171 0 167.172.001-9.858-19.749-26.507-35.515-46.891-44.231-11.519 3.91-23.862 6.031-36.695 6.031-12.834 0-25.176-2.121-36.695-6.031zm-394.033-57.964c44.853 32.045 74.097 84.535 74.097 143.86 0 23.012-18.655 41.667-41.667 41.667h-270.072c-23.011 0-41.666-18.655-41.666-41.667 0-59.325 29.244-111.815 74.097-143.86-7.446-15.165-11.628-32.218-11.628-50.239 0-63.048 51.186-114.234 114.233-114.234 63.048 0 114.234 51.186 114.234 114.234 0 18.021-4.182 35.074-11.628 50.239zm-139.301 57.964c-20.384 8.715-37.032 24.481-46.89 44.23 0 0 167.171 0 167.172.001-9.858-19.749-26.507-35.515-46.892-44.231-11.519 3.91-23.861 6.031-36.695 6.031-12.833 0-25.175-2.121-36.695-6.031zm36.695-139.104c-17.054 0-30.9 13.846-30.9 30.901 0 17.054 13.846 30.9 30.9 30.9 17.055 0 30.901-13.846 30.901-30.9 0-17.055-13.846-30.901-30.901-30.901zm-430.727 81.14c44.853 32.045 74.097 84.535 74.097 143.86 0 23.012-18.655 41.667-41.667 41.667h-270.072c-23.012 0-41.667-18.655-41.667-41.667 0-59.325 29.244-111.815 74.097-143.86-7.446-15.165-11.628-32.218-11.628-50.239 0-63.048 51.187-114.234 114.234-114.234s114.234 51.186 114.234 114.234c0 18.021-4.182 35.074-11.628 50.239zm-102.606-81.14c-17.054 0-30.9 13.846-30.9 30.901 0 17.054 13.846 30.9 30.9 30.9s30.901-13.846 30.901-30.9c0-17.055-13.847-30.901-30.901-30.901zm-36.695 139.104c-20.384 8.715-37.032 24.481-46.89 44.23 0 0 167.17 0 167.171.001-9.858-19.749-26.507-35.515-46.891-44.231-11.519 3.91-23.861 6.031-36.695 6.031s-25.176-2.121-36.695-6.031zm-96.638-1022.44h266.666c23.012 0 41.667 18.655 41.667 41.667v666.666c0 23.012-18.655 41.667-41.667 41.667h-266.666c-23.012 0-41.667-18.655-41.667-41.667v-666.666c0-23.012 18.655-41.667 41.667-41.667zm41.666 83.333v583.334h183.334v-583.334zm491.667-750h266.667c23.012 0 41.666 18.655 41.666 41.667v1333.33c0 23.012-18.654 41.667-41.666 41.667h-266.667c-23.012 0-41.667-18.655-41.667-41.667v-1333.33c0-23.012 18.655-41.667 41.667-41.667zm41.667 83.334v1250h183.333v-1250zm491.666 850h266.667c23.012 0 41.667 18.654 41.667 41.666v400c0 23.012-18.655 41.667-41.667 41.667h-266.667c-23.011 0-41.666-18.655-41.666-41.667v-400c0-23.012 18.655-41.666 41.666-41.666zm41.667 83.333v316.667h183.333v-316.667zm491.667-750h266.666c23.012 0 41.667 18.655 41.667 41.667v1066.67c0 23.012-18.655 41.667-41.667 41.667h-266.666c-23.012 0-41.667-18.655-41.667-41.667v-1066.67c0-23.012 18.655-41.667 41.667-41.667zm41.666 83.333v983.334h183.334v-983.334z" />
                            </svg>
                            Poll / Survey
                            <span>3</span>{" "}
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <svg
                              height="20"
                              viewBox="-56 0 512 512"
                              width="20"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path d="m70 166c5.519531 0 10-4.480469 10-10s-4.480469-10-10-10-10 4.480469-10 10 4.480469 10 10 10zm0 0" />
                              <path d="m150 512h160c5.523438 0 10-4.476562 10-10v-10h70c5.523438 0 10-4.476562 10-10 0-7.617188 0-457.015625 0-472 0-5.523438-4.476562-10-10-10h-300c-49.425781 0-90 39.941406-90 90v342c0 33.085938 26.914062 60 60 60h80v10c0 5.523438 4.476562 10 10 10zm150-20h-140v-20h140zm-120-61.71875c0-7.894531-3.203125-15.625-8.789062-21.210938-19.664063-19.667968-31.210938-46.636718-31.210938-75.351562v-77.71875c0-5.515625 4.484375-10 10-10 5.519531 0 10 4.480469 10 10v66c0 5.523438 4.476562 10 10 10s10-4.476562 10-10c0-50.355469 0-116.558594 0-166 0-5.515625 4.484375-10 10-10 5.519531 0 10 4.480469 10 10v100c0 5.523438 4.476562 10 10 10s10-4.476562 10-10v-120c0-5.515625 4.484375-10 10-10 5.519531 0 10 4.480469 10 10v120c0 5.523438 4.476562 10 10 10s10-4.476562 10-10v-100c0-5.515625 4.484375-10 10-10 5.519531 0 10 4.480469 10 10v100c0 5.523438 4.476562 10 10 10s10-4.476562 10-10c0-9.101562 0-44.226562 0-60 0-5.523438 4.480469-10 10-10 5.515625 0 10 4.484375 10 10v137.71875c0 28.601562-11.464844 55.605469-31.210938 75.351562-5.585937 5.585938-8.789062 13.316407-8.789062 21.210938v21.71875h-100zm200-8.28125h-75.910156c8.699218-8.929688 15.976562-18.996094 21.671875-30h54.238281zm-60 50v-10c0-5.523438-4.476562-10-10-10h-10v-10h80v30zm-260-445.265625v89.265625c0 5.523438 4.476562 10 10 10s10-4.476562 10-10v-95.28125c3.304688-.472656 6.648438-.71875 10-.71875h290v352h-45.929688c3.933594-12.363281 5.929688-25.191406 5.929688-38.28125v-137.71875c0-16.542969-13.457031-30-30-30-3.460938 0-6.828125.585938-10 1.703125v-11.703125c0-16.574219-13.425781-30-30-30-3.902344 0-7.628906.757812-11.050781 2.117188-3.421875-12.632813-14.988281-22.117188-28.949219-22.117188-13.808594 0-25.464844 9.382812-28.9375 22.105469-3.476562-1.378907-7.210938-2.105469-11.0625-2.105469-16.542969 0-30 13.457031-30 30v71.703125c-3.171875-1.117187-6.539062-1.703125-10-1.703125-16.542969 0-30 13.457031-30 30v77.71875c0 13.09375 2 25.925781 5.929688 38.28125h-45.929688v-176c0-5.523438-4.476562-10-10-10s-10 4.476562-10 10v176c-15.355469 0-29.375 5.804688-40 15.328125v-297.328125c0-27.449219 15.929688-51.875 40-63.265625zm0 445.265625c-22.054688 0-40-17.945312-40-40s17.945312-40 40-40h74.242188c5.703124 11.019531 12.976562 21.074219 21.667968 30h-85.910156c-5.523438 0-10 4.476562-10 10s4.476562 10 10 10h90v10h-10c-5.523438 0-10 4.476562-10 10v10zm0 0" />
                            </svg>
                            Pledge
                            <span>0</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <svg
                              id="Capa_1"
                              enableBackground="new 0 0 512 512"
                              height="20"
                              viewBox="0 0 512 512"
                              width="20"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <defs></defs>
                              <path d="m349.19 465.94h-62.39v-38.35c0-4.142-3.358-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v38.35h-31.6v-109.254h31.6v38.224c0 4.142 3.358 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-38.224c54.6-.878 100.95-44.969 100.95-100.5v-93.373c0-4.142-3.358-7.5-7.5-7.5h-23.563s.002-85.973-.001-86.053c-.13-38.213-31.257-69.26-69.499-69.26h-63.758c-38.322 0-69.5 31.178-69.5 69.5v85.812h-22.178c-4.142 0-7.5 3.358-7.5 7.5v93.373c0 55.578 46.306 99.542 100.95 100.5v109.255h-62.39c-21.033 0-38.56 17.527-38.56 38.56 0 4.142 3.358 7.5 7.5 7.5h63.63c4.142 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-54.916c3.645-9.834 11.093-15.187 22.346-16.06h186.379c10.371 0 19.197 6.734 22.336 16.06h-143.456c-4.142 0-7.5 3.358-7.5 7.5s3.358 7.5 7.5 7.5h152.18c4.142 0 7.5-3.358 7.5-7.5 0-21.262-17.297-38.56-38.56-38.56zm-62.128-201.91c-4.142 0-7.5 3.358-7.5 7.5v39.093h-16.754v-39.093c0-4.142-3.358-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v39.093h-15.37v-39.093c0-4.142-3.358-7.5-7.5-7.5s-7.5 3.358-7.5 7.5v38.766c-27.243-2.992-48.509-26.146-48.509-54.173v-23.686h172.758v23.686c0 27.55-20.549 50.392-47.125 54.003v-38.596c0-4.142-3.358-7.5-7.5-7.5zm-69.624-248.703v23.235c0 4.142 3.358 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-23.562h16.062v23.563c0 4.142 3.358 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-23.563h16.062v23.563c0 4.142 3.358 7.5 7.5 7.5s7.5-3.358 7.5-7.5v-23.066c24.121 3.278 43.277 22.397 46.61 46.501h-68.832c-4.142 0-7.5 3.358-7.5 7.5s3.358 7.5 7.5 7.5h69.347v16.25h-54.625c-4.142 0-7.5 3.358-7.5 7.5s3.358 7.5 7.5 7.5h54.625v16.001h-54.625c-4.142 0-7.5 3.358-7.5 7.5s3.358 7.5 7.5 7.5h54.625v16.124h-54.625c-4.142 0-7.5 3.358-7.5 7.5s3.358 7.5 7.5 7.5h54.625v16.001h-54.625c-4.142 0-7.5 3.358-7.5 7.5s3.358 7.5 7.5 7.5h54.625v16.064h-172.758v-16.003h56.009c4.142 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-56.009v-16.062h56.009c4.142 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-56.009v-16.063h56.009c4.142 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-56.009v-16.062h56.009c4.142 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-56.009v-16.25h70.731c4.142 0 7.5-3.358 7.5-7.5s-3.358-7.5-7.5-7.5h-70.217c3.397-24.563 23.225-43.95 47.995-46.671zm-78.187 240.859v-85.873h14.678v85.811c0 38.322 31.178 69.5 69.5 69.5h63.758c38.322 0 69.5-31.178 69.5-69.5v-85.811h16.063v85.873c0 47.145-38.355 85.5-85.5 85.5h-62.498c-47.146 0-85.501-38.355-85.501-85.5z" />
                            </svg>
                            Podcast
                            <span>3</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <svg
                              id="Layer_3"
                              height="20"
                              viewBox="0 0 64 64"
                              width="20"
                              xmlns="http://www.w3.org/2000/svg"
                              data-name="Layer 3"
                            >
                              <path d="m32 20v-4a4 4 0 0 0 -8 0v4a4 4 0 0 0 4 4 3.947 3.947 0 0 0 2.019-.567l1.274 1.274 1.414-1.414-1.274-1.274a3.947 3.947 0 0 0 .567-2.019zm-4 2a2 2 0 0 1 -2-2v-4a2 2 0 0 1 4 0v4a1.96 1.96 0 0 1 -.075.511l-1.218-1.218-1.414 1.414 1.218 1.218a1.96 1.96 0 0 1 -.511.075z" />
                              <path d="m38 21a1 1 0 0 1 -2 0v-9h-2v9a3 3 0 0 0 6 0v-9h-2z" />
                              <path d="m42 12h2v12h-2z" />
                              <path d="m42 8h2v2h-2z" />
                              <path d="m53.87 12.507a1 1 0 0 0 -.87-.507h-7v2h5.234l-5.091 8.485a1 1 0 0 0 .857 1.515h7v-2h-5.234l5.091-8.485a1 1 0 0 0 .013-1.008z" />
                              <path d="m39 1c-13.233 0-24 7.178-24 16a12.958 12.958 0 0 0 4.716 9.547l-2.6 4.991a1 1 0 0 0 1.075 1.444l9.576-1.832a31.92 31.92 0 0 0 5.133 1.328l-25.9 6.252v-.73a1 1 0 0 0 -1-1h-4a1 1 0 0 0 -1 1v14a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-.73l3 .724v4.006a7.008 7.008 0 0 0 7 7h8a7.008 7.008 0 0 0 6.878-5.725l2.122.512v2.213a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-27.141c12.033-.988 21-7.687 21-15.859 0-8.822-10.767-16-24-16zm-34 50h-2v-12h2zm20 10h-8a5.006 5.006 0 0 1 -5-5v-3.523l2 .483v2.04a4 4 0 0 0 4 4h6a3.993 3.993 0 0 0 3.77-2.716l2.149.518a5 5 0 0 1 -4.919 4.198zm-9-7.557 9.823 2.371a2 2 0 0 1 -1.823 1.186h-6a2 2 0 0 1 -2-2zm-9-4.23v-8.426l27-6.517v21.46zm33 9.787h-4v-28h4zm2-28.147v-.853a1 1 0 0 0 -1-1h-6a1 1 0 0 0 -1 1v.621a30.167 30.167 0 0 1 -5.815-1.444 1.014 1.014 0 0 0 -.529-.042l-7.813 1.494 2.013-3.86a1 1 0 0 0 -.252-1.234c-3.012-2.479-4.604-5.435-4.604-8.535 0-7.72 9.869-14 22-14s22 6.28 22 14c0 6.96-8.267 12.908-19 13.853z" />
                              <path
                                d="m44.172 35h5.657v2h-5.657z"
                                transform="matrix(.707 -.707 .707 .707 -11.69 43.778)"
                              />
                              <path
                                d="m46 53.172h2v5.657h-2z"
                                transform="matrix(.707 -.707 .707 .707 -25.832 49.636)"
                              />
                              <path d="m45 45h6v2h-6z" />
                            </svg>
                            Quiz
                            <span>4</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <svg
                              height="20"
                              viewBox="0 0 60 60"
                              width="20"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <g id="Page-1" fill="none" fill-rule="evenodd">
                                <g
                                  id="060---Blog-Writing"
                                  fill="rgb(0,0,0)"
                                  fill-rule="nonzero"
                                  transform="translate(-1)"
                                >
                                  <path
                                    id="Shape"
                                    d="m57.218.9c-.5698338-.57620291-1.3466172-.90031284-2.157-.9h-.01c-.8054418.00094615-1.5767888.32519291-2.141.9l-2.103 2.1h-46.807c-1.7044406.05441939-3.04487651 1.47528146-3 3.18v41.64c-.04487651 1.7047185 1.2955594 3.1255806 3 3.18h15.234l-1.8 3h-3.434c-1.6568542 0-3 1.3431458-3 3s1.3431458 3 3 3h30c1.6568542 0 3-1.3431458 3-3s-1.3431458-3-3-3h-3.434l-1.8-3h15.234c1.7044406-.0544194 3.0448765-1.4752815 3-3.18v-36.626l.921-.922c.005-.005.011-.006.016-.01s.005-.011.01-.016l2.153-2.154c.5754021-.56948475.8991618-1.34543193.8991618-2.155s-.3237597-1.58551525-.8991618-2.155zm-7.13 10.014c.3904999.3903819 1.0235001.3903819 1.414 0l2.835-2.835 1.475 1.475-9.285 9.286-1.475-1.476 3.558-3.564c.3397768-.3967603.3169323-.9881942-.0524368-1.3575632-.369369-.3693691-.9608029-.3922136-1.3575632-.0524368l-3.558 3.558-1.48-1.479 9.29-9.282 1.475 1.476-2.839 2.837c-.3890509.39026975-.3890509 1.0217302 0 1.412zm-8.836 5.479 3.348 3.35-4.6 1.226zm3.748 40.607c0 .5522847-.4477153 1-1 1h-30c-.5522847 0-1-.4477153-1-1s.4477153-1 1-1h30c.5522847 0 1 .4477153 1 1zm-6.767-3h-18.466l1.8-3h14.867zm16.767-10h-45c-.55228475 0-1 .4477153-1 1s.44771525 1 1 1h45v1.82c.0374856.5980051-.4039453 1.1188935-1 1.18h-50c-.59605473-.0611065-1.03748559-.5819949-1-1.18v-1.82h3c.55228475 0 1-.4477153 1-1s-.44771525-1-1-1h-3v-37.82c-.03748559-.59800508.40394527-1.11889349 1-1.18h44.806l-8.612 8.605c-.2335384.2487806-.4048756.549306-.5.877l-1.628 5.967c-.1621765.607572-.0333272 1.2560039.3488168 1.7554144s.9743433.7932916 1.6031832.7955856c.1819916-.0000984.3631909-.0239669.539-.071l5.978-1.629c.3329578-.0994827.6371545-.2774738.887-.519l7.578-7.581zm3.691-37.328-1.465 1.465-2.179-2.179-2.18-2.18 1.466-1.464c.1898673-.1975301.4510356-.31064299.725-.314.2796094.00052297.5473004.11329183.743.313l2.887 2.887c.198624.19594113.3109166.46299597.312.742-.0007053.27497253-.1120771.53808396-.309.73z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m20 24c1.6568542 0 3-1.3431458 3-3v-10c0-1.65685425-1.3431458-3-3-3h-12c-1.65685425 0-3 1.34314575-3 3v10c0 1.6568542 1.34314575 3 3 3zm0-2h-11.659l4.38-4.606 2.61 2.349c.4103865.3693686 1.0424713.3362424 1.412-.074.3098376-.3502305.3307299-.8700328.05-1.244l1.932-2.03 2.275 2.05v2.555c0 .5522847-.4477153 1-1 1zm-12-12h12c.5522847 0 1 .4477153 1 1v4.753l-.937-.845c-.8033827-.7315537-2.0451618-.6830224-2.789.109l-1.939 2.04-1.275-1.149c-.8043258-.7206056-2.0344279-.6764368-2.785.1l-4.275 4.499v-9.507c0-.5522847.44771525-1 1-1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m35 29v10c0 1.6568542 1.3431458 3 3 3h12c1.6568542 0 3-1.3431458 3-3v-10c0-1.6568542-1.3431458-3-3-3h-12c-1.6568542 0-3 1.3431458-3 3zm15 11h-11.659l4.38-4.606 2.61 2.349c.4103865.3693686 1.0424713.3362424 1.412-.074.3098376-.3502305.3307299-.8700328.05-1.244l1.932-2.03 2.275 2.05v2.555c0 .5522847-.4477153 1-1 1zm1-11v4.753l-.937-.845c-.8035817-.731008-2.0449209-.6824939-2.789.109l-1.939 2.04-1.275-1.149c-.8041357-.7211308-2.0346553-.6769469-2.785.1l-4.275 4.499v-9.507c0-.5522847.4477153-1 1-1h12c.5522847 0 1 .4477153 1 1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m26 23h9c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1h-9c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m31 27h-24c-.55228475 0-1 .4477153-1 1s.44771525 1 1 1h24c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m31 31h-24c-.55228475 0-1 .4477153-1 1s.44771525 1 1 1h24c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m31 35h-24c-.55228475 0-1 .4477153-1 1s.44771525 1 1 1h24c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m31 39h-24c-.55228475 0-1 .4477153-1 1s.44771525 1 1 1h24c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m26 19h9c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1h-9c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m26 15h10c.5522847 0 1-.4477153 1-1s-.4477153-1-1-1h-10c-.5522847 0-1 .4477153-1 1s.4477153 1 1 1z"
                                  />
                                  <path
                                    id="Shape"
                                    d="m26 11h3c.5522847 0 1-.4477153 1-1 0-.55228475-.4477153-1-1-1h-3c-.5522847 0-1 .44771525-1 1 0 .5522847.4477153 1 1 1z"
                                  />
                                </g>
                              </g>
                            </svg>
                            Blog
                            <span>2</span>
                          </a>
                        </li>
                        <li>
                          <a href="#">
                            <svg
                              enableBackground="new 0 0 550 550"
                              viewBox="0 0 550 550"
                              height="20"
                              width="20"
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <g id="_x31_8622_-_Advertising_Campaign">
                                <g>
                                  <g>
                                    <g>
                                      <g>
                                        <path d="m251.972 530.615c-14.708.001-28.911-7.663-36.717-21.043l-81.002-138.879c-2.226-3.816-.937-8.715 2.88-10.94 3.818-2.226 8.715-.937 10.941 2.88l81.001 138.878c6.704 11.49 20.985 16.214 33.217 10.987 6.526-2.788 11.562-7.951 14.178-14.538 2.598-6.541 2.519-13.68-.225-20.101-.393-.919-.852-1.841-1.363-2.739l-75.18-132.093c-2.186-3.84-.844-8.725 2.996-10.91 3.839-2.187 8.724-.845 10.91 2.996l75.18 132.092c.809 1.421 1.54 2.892 2.171 4.367 4.414 10.333 4.55 21.802.382 32.294-4.202 10.579-12.285 18.87-22.762 23.345-5.393 2.305-11.037 3.404-16.607 3.404z" />
                                      </g>
                                      <g>
                                        <path d="m90.278 373.158c-7.528 0-15.061-1.441-22.258-4.33-14.808-5.943-26.416-17.297-32.685-31.971-12.94-30.29 1.175-65.461 31.465-78.402 4.061-1.735 8.764.151 10.5 4.214s-.151 8.764-4.214 10.5c-22.177 9.474-32.512 35.225-23.037 57.403 4.589 10.743 13.088 19.056 23.93 23.407s22.73 4.22 33.472-.37c4.062-1.74 8.764.15 10.5 4.214 1.736 4.063-.151 8.764-4.214 10.499-7.541 3.223-15.498 4.836-23.459 4.836z" />
                                      </g>
                                      <g>
                                        <path d="m126.489 379.436c-9.812.001-19.16-5.75-23.251-15.327l-40.651-95.153c-5.469-12.802.497-27.666 13.297-33.134l58.667-25.063c66.437-28.383 121.848-75.764 160.244-137.022 2.346-3.745 7.281-4.877 11.027-2.53 3.743 2.346 4.876 7.283 2.529 11.027-40.137 64.036-98.063 113.567-167.514 143.239l-58.667 25.062c-4.688 2.002-6.873 7.446-4.87 12.135l40.651 95.153c2.003 4.688 7.447 6.873 12.135 4.869l58.666-25.063c69.451-29.672 145.283-37.284 219.302-22.018 4.327.893 7.111 5.124 6.219 9.451-.893 4.325-5.113 7.11-9.451 6.219-70.807-14.604-143.349-7.322-209.784 21.061l-58.667 25.063c-3.223 1.378-6.58 2.031-9.882 2.031z" />
                                      </g>
                                      <g>
                                        <path d="m331.052 181.015c-1.062 0-2.122-.211-3.119-.633-2.002-.848-3.573-2.474-4.352-4.504-12.238-31.939-21.476-61.82-26.01-84.139-5.739-28.25-3.855-42.747 6.108-47.004h.001c9.964-4.254 21.74 4.403 38.187 28.08 12.992 18.705 28.197 46.037 42.813 76.96.93 1.966 1.019 4.225.247 6.258s-2.337 3.664-4.337 4.518l-46.396 19.821c-1.003.429-2.073.643-3.142.643zm-20.435-120.043c-.556 3.898-.684 13.164 3.837 33.18 4.313 19.099 11.582 42.553 21.132 68.226l31.042-13.261c-11.948-24.649-23.869-46.116-34.688-62.436-11.338-17.103-18.122-23.415-21.323-25.709z" />
                                      </g>
                                      <g>
                                        <path d="m426.902 345.952c-8.9 0-19.396-9.295-33.168-29.122-12.992-18.705-28.197-46.037-42.813-76.96-.93-1.966-1.019-4.225-.247-6.258s2.337-3.664 4.337-4.518l46.396-19.821c1.998-.855 4.26-.858 6.262-.01s3.573 2.474 4.352 4.504c12.239 31.939 21.477 61.82 26.011 84.138 5.739 28.25 3.854 42.747-6.109 47.004-1.622.693-3.292 1.043-5.021 1.043zm-57.928-105.425c11.948 24.648 23.869 46.115 34.688 62.436 11.339 17.104 18.122 23.417 21.322 25.71.557-3.898.685-13.163-3.836-33.181-4.313-19.099-11.583-42.553-21.133-68.226z" />
                                      </g>
                                      <g>
                                        <path d="m358.157 244.453c-3.005 0-5.874-1.702-7.236-4.583-4.858-10.277-9.658-20.957-14.266-31.741-4.604-10.778-9.003-21.629-13.074-32.251-1.538-4.016.373-8.53 4.328-10.22l46.986-20.073c10.438-4.458 21.985-4.586 32.52-.359 10.533 4.227 18.79 12.303 23.248 22.74 4.46 10.438 4.588 21.986.36 32.52s-12.304 18.79-22.741 23.249l-46.986 20.073c-1.024.437-2.09.644-3.139.645zm-16.846-67.121c3.194 8.121 6.565 16.336 10.058 24.511 3.495 8.178 7.101 16.293 10.758 24.211l39.87-17.033c6.508-2.78 11.543-7.928 14.179-14.495 2.635-6.567 2.556-13.767-.225-20.274s-7.928-11.542-14.494-14.177c-6.568-2.636-13.768-2.556-20.275.224zm36.727-24.39h.01z" />
                                      </g>
                                      <g>
                                        <path d="m233.994 338.022c-3.107 0-6.063-1.821-7.361-4.859l-56.38-131.971c-1.736-4.063.151-8.764 4.214-10.5 4.062-1.736 8.764.15 10.5 4.214l56.38 131.972c1.736 4.063-.151 8.764-4.214 10.499-1.025.438-2.091.645-3.139.645z" />
                                      </g>
                                      <g>
                                        <path d="m106.973 277.33c-3.107 0-6.063-1.821-7.361-4.859-1.736-4.063.151-8.764 4.214-10.5l54.402-23.242c4.064-1.735 8.763.151 10.5 4.214 1.736 4.063-.151 8.764-4.214 10.5l-54.402 23.241c-1.025.439-2.091.646-3.139.646z" />
                                      </g>
                                      <g>
                                        <path d="m120.454 308.884c-3.107 0-6.063-1.821-7.361-4.859-1.736-4.063.151-8.764 4.214-10.499l54.402-23.242c4.064-1.735 8.764.151 10.5 4.214s-.151 8.764-4.214 10.5l-54.402 23.241c-1.025.438-2.091.645-3.139.645z" />
                                      </g>
                                      <g>
                                        <path d="m133.934 340.438c-3.107 0-6.063-1.821-7.361-4.859-1.736-4.063.151-8.764 4.214-10.499l54.402-23.241c4.064-1.739 8.764.15 10.5 4.214 1.736 4.063-.151 8.764-4.214 10.499l-54.402 23.241c-1.025.438-2.091.645-3.139.645z" />
                                      </g>
                                      <g>
                                        <path d="m229.662 387.487c-3.107 0-6.063-1.821-7.361-4.859-1.736-4.063.151-8.764 4.214-10.499l8.8-3.76c7.768-3.318 13.778-9.463 16.925-17.303 3.146-7.839 3.051-16.434-.268-24.202-1.736-4.063.151-8.764 4.214-10.499 4.062-1.739 8.764.15 10.5 4.214 4.998 11.697 5.14 24.642.402 36.447s-13.79 21.059-25.488 26.056l-8.8 3.76c-1.024.438-2.091.645-3.138.645z" />
                                      </g>
                                    </g>
                                  </g>
                                  <g>
                                    <path d="m487.398 473.61h-129.848c-4.418 0-8-3.582-8-8v-81.155c0-4.418 3.582-8 8-8h129.849c4.418 0 8 3.582 8 8v81.155c-.001 4.418-3.583 8-8.001 8zm-121.848-16h113.849v-65.155h-113.849z" />
                                  </g>
                                  <g>
                                    <path d="m422.453 441.148c-1.688 0-3.378-.533-4.8-1.6l-64.925-48.693c-3.535-2.651-4.251-7.666-1.601-11.2 2.653-3.536 7.666-4.25 11.2-1.601l60.125 45.094 60.124-45.094c3.531-2.649 8.547-1.936 11.2 1.601 2.65 3.534 1.935 8.549-1.601 11.2l-64.924 48.693c-1.42 1.067-3.109 1.6-4.798 1.6z" />
                                  </g>
                                  <g>
                                    <path d="m445.998 140.874c-1.041 0-2.091-.203-3.09-.622-2.973-1.246-4.908-4.155-4.908-7.378v-28.874c-9.974-9.547-15.831-22.76-15.831-36.05 0-26.786 21.972-48.578 48.978-48.578 26.675 0 48.377 21.792 48.377 48.578 0 25.649-20.064 46.72-45.384 48.461l-22.529 22.166c-1.527 1.501-3.553 2.297-5.613 2.297zm25.148-105.502c-18.184 0-32.978 14.614-32.978 32.578 0 9.807 4.827 19.633 12.913 26.284 1.848 1.52 2.918 3.786 2.918 6.178v13.368l11.136-10.956c1.496-1.472 3.511-2.297 5.61-2.297 18.073 0 32.777-14.614 32.777-32.578s-14.523-32.577-32.376-32.577z" />
                                  </g>
                                  <g>
                                    <path d="m478 77h-16c-4.418 0-8-3.582-8-8s3.582-8 8-8h16c4.418 0 8 3.582 8 8s-3.582 8-8 8z" />
                                  </g>
                                </g>
                              </g>
                              <g id="Layer_1" />
                            </svg>
                            Campaign
                            <span>2</span>
                          </a>
                        </li>
                      </ul>
                    </div>
                  </div>
                  <div className="side-bar-widget wow fadeInUp">
                    <div className="category-widget dia-headline ul-li-block">
                      <h3 className="widget-title-2">Other Discussions</h3>
                      <div className="recent-post-area">
                        {discussions.map((item) => (
                          <div key={item.id} className="recent-post-img-text">
                            <a href={item.link}>
                              <div className="recent-post-img float-left">
                                <img src={item.image} alt={item.title} />
                              </div>
                              <div className="recent-post-text dia-headline">
                                <h3>
                                  <a href={item.link}>{item.title}</a>
                                </h3>
                                <span className="rec-post-meta">
                                  <a href={item.link}>
                                    Last Date :{" "}
                                    <span className="day">{item.date}</span>
                                  </a>
                                </span>
                              </div>
                            </a>
                          </div>
                        ))}
                      </div>
                    </div>
                  </div>
                  <div
                    className="side-bar-widget"
                    style={{ overflow: "hidden" }}
                  >
                    <div className="popular-widget dia-headline ul-li">
                      <h3 className="widget-title-2">Newsletters</h3>
                      <div
                        className="it-nx-testimonial-content pt-0 wow fadeInUp"
                        data-wow-delay="200ms"
                        data-wow-duration="1500ms"
                      >
                        <Swiper
                          // modules={[Autoplay]}
                          slidesPerView={2}
                          spaceBetween={30}
                          loop={true}
                          autoplay={{
                            delay: 2500,
                            disableOnInteraction: false,
                          }}
                          breakpoints={{
                            0: { slidesPerView: 1 },
                            768: { slidesPerView: 2 },
                            1024: { slidesPerView: 2 },
                          }}
                          onSlideChange={(swiper) => {
                            const groupIndex = Math.floor(swiper.realIndex / 3);
                            setActiveGroup(groupIndex);
                          }}
                        >
                          {data.map((item) => (
                            <SwiperSlide key={item.id}>
                              <div className="it-nx-testimonial-slider">
                                <div className="it-nw-testimonial-innerbox p-0 position-relative">
                                  <img src={item.img} alt={item.title} />
                                </div>
                              </div>  
                            </SwiperSlide>
                          ))}
                        </Swiper>

                        {/* Custom Pagination Dots */}
                        <div className="custom-pagination">
                          {Array.from({ length: totalGroups }).map((_, i) => (
                            <span
                              key={i}
                              className={`custom-dot ${
                                i === activeGroup ? "active" : ""
                              }`}
                            ></span>
                          ))}
                        </div>

                        {/* <Slider
                          {...settings}
                          className="it-nx-testimonial-slider"
                        >
                          {newsletters.map((item) => (
                            <div
                              key={item.id}
                              className="it-nw-testimonial-innerbox p-0 position-relative"
                            >
                              <img
                                src={item.image}
                                alt={`Newsletter ${item.id}`}
                                style={{ width: "100%", borderRadius: "8px" }}
                              />
                            </div>
                          ))}
                        </Slider> */}
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              {/* End Sidebar */}
            </div>
          </div>
        </div>
      </section>
    </>
  );
};

export default NewsFeed;
