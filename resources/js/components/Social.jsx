import { useEffect, useState } from "react";
import { Swiper, SwiperSlide } from "swiper/react";
import { Autoplay } from "swiper/modules";
import "swiper/css";
import "swiper/css/pagination";
// import "./EconomySlider.css";
const NewsFeedSection = () => {
    const [activeGroup, setActiveGroup] = useState(0);
    const shapes = [
        "Vector (1).svg",
        "Vector (2).svg",
        "Vector (5).svg",
        "Vector (3).svg",
        "Vector (4).svg",
    ];

    const data = [
        { id: 1, title: "Sample Heading", img: "/design/assets/new/dfds.jpg" },
        { id: 2, title: "Sample Heading", img: "/design/assets/new/dfds.jpg" },
        { id: 3, title: "Sample Heading", img: "/design/assets/new/dfds.jpg" },
        { id: 4, title: "Sample Heading", img: "/design/assets/new/dfds.jpg" },
        { id: 5, title: "Sample Heading", img: "/design/assets/new/dfds.jpg" },
        { id: 6, title: "Sample Heading", img: "/design/assets/new/dfds.jpg" },
    ];

    const totalGroups = Math.ceil(data.length / 3);

    useEffect(() => {
        // Initialize Twitter and Facebook embeds after component mount
        const twitterScript = document.createElement("script");
        twitterScript.src = "https://platform.twitter.com/widgets.js";
        twitterScript.async = true;
        document.body.appendChild(twitterScript);

        const fbScript = document.createElement("script");
        fbScript.async = true;
        fbScript.defer = true;
        fbScript.crossOrigin = "anonymous";
        fbScript.src =
            "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v17.0";
        document.body.appendChild(fbScript);
    }, []);

    return (
        <>
            <section
                id="saasio-breadcurmb"
                className="saasio-breadcurmb-section"
            >
                <div className="container">
                    <div className="breadcurmb-title ">
                        <h2>Social</h2>
                    </div>
                    <div className="breadcurmb-item-list ul-li">
                        <ul className="saasio-page-breadcurmb">
                            <li>
                                <a href="/">Home</a>
                            </li>
                            <li>
                                <a href="#">Social</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
            <section
                id="news-feed"
                className="dept_inner news-feed-section position-relative pb-0"
            >
                <div className="it-nw-side-bg text-center position-absolute">
                    <img
                        src="/design/assets/background/Component 574.svg"
                        alt=""
                    />
                </div>

                <div className="blog-feed-content">
                    <div className="saasio-blog-details-content">
                        {/* <div className="blog-details-text dia-headline wow fadeInTop mb-4 pb-3 text-center">
                          <span className="eg-title-tag">
                              Social Medias{" "}
                              <i className="square-shape">
                                  <i></i>
                                  <i></i>
                                  <i></i>
                                  <i></i>
                              </i>
                          </span>
                          <h1 className="inhead Fword mt-2">
                              <span>Connect</span> with us
                          </h1>
                      </div> */}

                        {/* ===== Social Media Section ===== */}
                        <div
                            id="it-nw-fun-fact"
                            className="social3 it-nw-fun-fact-section pb-5 mb-5 position-relative"
                        >
                            <div className="container-fluid">
                                <div
                                    className="col-md-10 mx-auto pt-0 aplpg-pricing-table"
                                    data-background="/design/assets/app-landing-2/pricing-bg.jpg"
                                >
                                    <div className="aplpg-pricing-content">
                                        <div className="row justify-content-center">
                                            {/* Facebook */}
                                            <div className="col-xl-3 col-md-6">
                                                <div className="aplpg-pricing-column fb">
                                                    <div className="aplpg-pricing-top aplpg-headline">
                                                        <p>Facebook</p>
                                                        <h3>
                                                            <img
                                                                src="/design/assets/new/facebook.svg"
                                                                width="35"
                                                                alt="Facebook"
                                                            />
                                                        </h3>
                                                        <span className="aplpg-triangle-shape"></span>
                                                    </div>
                                                    <div className="socialbody">
                                                        <div
                                                            className="fb-page"
                                                            data-href="https://www.facebook.com/keralainformation/"
                                                            data-tabs="timeline"
                                                            data-width=""
                                                            data-height="620"
                                                            data-small-header="true"
                                                            data-adapt-container-width="true"
                                                            data-hide-cover="true"
                                                            data-show-facepile="true"
                                                        >
                                                            <blockquote
                                                                cite="https://www.facebook.com/keralainformation/"
                                                                className="fb-xfbml-parse-ignore"
                                                            >
                                                                <a href="https://www.facebook.com/keralainformation/">
                                                                    Kerala
                                                                    Government
                                                                </a>
                                                            </blockquote>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Instagram */}
                                            <div className="col-lg-3 col-md-6">
                                                <div className="aplpg-pricing-column insta">
                                                    <div className="aplpg-pricing-top aplpg-headline">
                                                        <p>Instagram</p>
                                                        <h3>
                                                            <img
                                                                src="/design/assets/new/instagram.svg"
                                                                width="35"
                                                                alt="Instagram"
                                                            />
                                                        </h3>
                                                        <span className="aplpg-triangle-shape"></span>
                                                    </div>
                                                    <div className="socialbody  insta-body">
                                                        <iframe
                                                            src="https://www.instagram.com/reel/DPfUJS2DwYU/embed/"
                                                            width="320"
                                                            height="440"
                                                            frameBorder="0"
                                                            scrolling="no"
                                                            allowTransparency
                                                            allowFullScreen
                                                        ></iframe>
                                                        {/* <a
                                                            className="social-instagram-btn"
                                                            href="https://www.instagram.com/kerala_tourism/"
                                                            target="_blank"
                                                            rel="noreferrer"
                                                        >
                                                            View Profile
                                                        </a> */}
                                                    </div>
                                                </div>
                                            </div>

                                            {/* YouTube */}
                                            <div className="col-lg-3 col-md-6">
                                                <div className="aplpg-pricing-column ytb">
                                                    <div className="aplpg-pricing-top aplpg-headline">
                                                        <p>YouTube</p>
                                                        <h3>
                                                            <img
                                                                src="/design/assets/new/ytb.png"
                                                                width="35"
                                                                alt="YouTube"
                                                            />
                                                        </h3>
                                                        <span className="aplpg-triangle-shape"></span>
                                                    </div>
                                                    <div className="socialbody">
                                                        {[
                                                            "https://www.youtube.com/embed/LimKSAU0ziM?si=D61zlLL14tCxQf0C",
                                                            "https://www.youtube.com/embed/GFcBM7X6fcs?si=Yk45pNPYEJDhPYQ5",
                                                            "https://www.youtube.com/embed/O9jENLz77AY?si=3LjiPx-AzXGEURij",
                                                        ].map((url, idx) => (
                                                            <iframe
                                                                key={idx}
                                                                className="w-100 mb-2"
                                                                src={url}
                                                                title={`YouTube video ${idx}`}
                                                                frameBorder="0"
                                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                                                referrerPolicy="strict-origin-when-cross-origin"
                                                                allowFullScreen
                                                            ></iframe>
                                                        ))}
                                                    </div>
                                                </div>
                                            </div>

                                            {/* Twitter */}
                                            <div className="col-lg-3 col-md-6">
                                                <div className="aplpg-pricing-column twi">
                                                    <div className="aplpg-pricing-top aplpg-headline">
                                                        <p>Twitter</p>
                                                        <h3>
                                                            <img
                                                                src="/design/assets/new/twitter.svg"
                                                                width="35"
                                                                alt="Twitter"
                                                            />
                                                        </h3>
                                                        <span className="aplpg-triangle-shape"></span>
                                                    </div>
                                                    <div className="socialbody">
                                                        <a
                                                            className="twitter-timeline"
                                                            href="https://twitter.com/iprdkerala?ref_src=twsrc%5Etfw"
                                                        >
                                                            Tweets by IPRD
                                                            Kerala
                                                        </a>
                                                    </div>
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

export default NewsFeedSection;
