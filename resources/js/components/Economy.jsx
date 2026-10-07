import {
    FaArrowRight,
    FaFacebookF,
    FaInstagram,
    FaWhatsapp,
    FaTwitter,
} from "react-icons/fa";
import KeralaNumberOneSlider from "./EconomySlider";
import { useParams, Link } from "react-router-dom";
import { useEffect, useState } from "react";
import { sectorsAPI, articlesAPI } from "../services/api";
import { useLanguage } from "./LanguageContext";
import NotFound from "./NotFound";
import axios from "axios";

const EconomySection = () => {
    const { sectorSlug } = useParams();
    const { language } = useLanguage();
    const [sectorArticles, setSectorArticles] = useState([]);
    const [loading, setLoading] = useState(true);
    const [currentSector, setCurrentSector] = useState(null);
    const [notFound, setNotFound] = useState(false);

    // Fetch articles by sector slug (keep both the old direct slug endpoint and the sectors API)
    useEffect(() => {
        async function loadSectorData() {
            try {
                setLoading(true);
                setNotFound(false);

                const origin = "/api";
                let gotArticlesFromDirect = false;
                let sectorFound = false;

                // 1) Try the old/direct slug route (it returns articles list at e.g. /api/<sectorSlug>)
                try {
                    const directUrl = `${origin}/${sectorSlug}`;
                    const directRes = await axios.get(directUrl);
                    if (
                        directRes?.data &&
                        directRes.data.status &&
                        Array.isArray(directRes.data.data)
                    ) {
                        setSectorArticles(directRes.data.data);
                        gotArticlesFromDirect = true;
                        sectorFound = true;
                    }
                } catch (e) {
                    // ignore — we'll fall back to the sectors API below
                    console.debug(
                        "Direct sector route fetch failed (or not present), continuing to sectors API",
                        e
                    );
                }

                // 2) Always call the sectors API to get the sector object (this contains counters)
                try {
                    const sectorsRes = await sectorsAPI.getAll();
                    if (
                        sectorsRes?.data?.data &&
                        Array.isArray(sectorsRes.data.data)
                    ) {
                        const sector = sectorsRes.data.data.find(
                            (s) =>
                                (s.entitle || "")
                                    .toLowerCase()
                                    .replace(/\s+/g, "-") === sectorSlug
                        );
                        if (sector) {
                            setCurrentSector(sector);
                            sectorFound = true;

                            // If direct fetch didn't return articles, fetch via articlesAPI
                            if (!gotArticlesFromDirect) {
                                try {
                                    const articlesRes =
                                        await articlesAPI.getBySector(
                                            sector.id
                                        );
                                    if (
                                        articlesRes?.data?.status &&
                                        Array.isArray(articlesRes.data.data)
                                    ) {
                                        setSectorArticles(
                                            articlesRes.data.data
                                        );
                                    }
                                } catch (e) {
                                    console.error(
                                        "Failed to fetch articles via articlesAPI:",
                                        e
                                    );
                                }
                            }
                        }
                    }
                } catch (e) {
                    console.error("Failed to fetch sectors list:", e);
                }

                // If sector was not found in either method, set 404
                if (!sectorFound) {
                    setNotFound(true);
                }
            } catch (error) {
                console.error("Failed to load sector data:", error);
                setNotFound(true);
            } finally {
                setLoading(false);
            }
        }
        if (sectorSlug) {
            loadSectorData();
        }
    }, [sectorSlug]);

    // Capitalize first letter helper
    const capitalizeFirst = (str) => {
        if (!str) return "";
        return str.charAt(0).toUpperCase() + str.slice(1);
    };

    // Get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === "ml" ? malText || enText : enText;
    };

    const shapes = [
        "Vector (1).svg",
        "Vector (2).svg",
        "Vector (5).svg",
        "Vector (3).svg",
        "Vector (4).svg",
    ];
    const shapeSizes = {
        "Vector (1).svg": 10,
        "Vector (2).svg": 14,
        "Vector (3).svg": 8,
        "Vector (4).svg": 16,
        "Vector (5).svg": 12,
    };

    const shape = [
        "Vector (1).svg",
        "Vector (2).svg",
        "Vector (5).svg",
        "Vector (3).svg",
        "Vector (4).svg",
    ];
    const shapeSize = {
        "Vector (1).svg": 0,
        "Vector (2).svg": 0,
        "Vector (3).svg": 0,
        "Vector (4).svg": 0,
        "Vector (5).svg": 30,
    };

    const shape_1 = [
        "Vector (1).svg",
        "Vector (2).svg",
        "Vector (5).svg",
        "Vector (3).svg",
        "Vector (4).svg",
    ];
    const shapeSize_1 = {
        "Vector (1).svg": 0,
        "Vector (2).svg": 0,
        "Vector (3).svg": 60,
        "Vector (4).svg": 0,
        "Vector (5).svg": 0,
    };

    // Helper functions to categorize articles by entitle
    const getProgressArticles = () => {
        // console.log(article,"article.posterarticle.poster");

        return sectorArticles.filter(
            (article) => article.articletype?.trim() === "Progress made"
        );
    };

    const getKeralaNumberOneArticles = () => {
        return sectorArticles.filter(
            (article) => article.articletype?.trim() === "Kerala No.1"
        );
    };

    const getOtherAchievementsArticles = () => {
        return sectorArticles.filter(
            (article) => article.articletype?.trim() === "Other Achievements"
        );
    };

    const getAwardsArticles = () => {
        return sectorArticles.filter(
            (article) => article.articletype?.trim() === "Awards & Recognitions"
        );
    };

    const getKeyNumbers = () => {
        return sectorArticles.filter(
            (article) => article.articletype?.trim() === "Key Numbers"
        );
    };

    // Extract counters from current sector
    const sectorCounters = currentSector?.counters || [];

    // Show 404 if sector not found
    if (!loading && notFound) {
        return <NotFound />;
    }
    // console.log(article.poster,"article.poster");

    return (
        <>
            <section
                id="saasio-breadcurmb"
                className="saasio-breadcurmb-section"
            >
                <div className="container">
                    <div className="breadcurmb-title">
                        <h2>
                            {capitalizeFirst(
                                getDisplayText(
                                    currentSector?.entitle || sectorSlug,
                                    currentSector?.maltitle
                                )
                            )}
                        </h2>
                    </div>
                    <div className="breadcurmb-item-list ul-li">
                        <ul className="saasio-page-breadcurmb">
                            <li>
                                <a href="/">Home</a>
                            </li>
                            <li>
                                <a href="/insights">Insights</a>
                            </li>
                            <li>
                                <a href="#">
                                    {capitalizeFirst(
                                        getDisplayText(
                                            currentSector?.entitle ||
                                                sectorSlug,
                                            currentSector?.maltitle
                                        )
                                    )}
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </section>
            <section
                id="news-feed"
                className="dept_inner news-feed-section position-relative"
            >
                {/* <div className="it-nw-side-bg text-center position-absolute">
                  <img src="/design/assets/its-2/side-line.png" alt="ente_keralam" />
              </div> */}

                <div className="blog-shapes-grid-right">
                    {Array.from({ length: 25 }).map((_, i) => {
                        const file = shapes[i % shapes.length];
                        return (
                            <div
                                className={`it-nw-blog-sh-bg sh${(i % 3) + 5}`}
                                key={i}
                            >
                                <img
                                    src={`/design/assets/bgv/${file}`}
                                    alt=""
                                    style={{
                                        width: shapeSizes[file] + "px",
                                    }}
                                />
                            </div>
                        );
                    })}
                </div>

                <div className="">
                    <div className="blog-feed-content">
                        <div className="saasio-blog-details-content">
                            {/* ================== HEADER ================== */}
                            <div className="blog-details-text dia-headline wow fadeInTop mb-4 pb-3 text-center">
                                <h1 className="inhead Fword mt-2">
                                    <span>
                                        {capitalizeFirst(
                                            getDisplayText(
                                                currentSector?.entitle ||
                                                    sectorSlug,
                                                currentSector?.maltitle
                                            )
                                        )}
                                    </span>
                                </h1>
                            </div>

                            {/* ================== FUN FACTS ================== */}
                            <div
                                id="it-nw-fun-fact"
                                className="it-nw-fun-fact-section pb-5 mb-5 position-relative"
                            >
                                <div className="container">
                                    <div className="it-nw-fun-fact-content position-relative">
                                        <div className="row">
                                            {/* Render API counters first (if any) */}
                                            {sectorCounters &&
                                                sectorCounters.length > 0 &&
                                                sectorCounters.map(
                                                    (counter, index) => (
                                                        <div
                                                            className="col-lg-4 col-md-6"
                                                            key={
                                                                counter.id ||
                                                                `api-${index}`
                                                            }
                                                        >
                                                            <div className="it-nw-fun-fact-innerbox d-flex align-items-center">
                                                                <div className="it-nw-fun-fact-icon d-flex justify-content-center align-items-center">
                                                                    {counter.icon && (
                                                                        <img
                                                                            // src={
                                                                            //     counter.icon.startsWith(
                                                                            //         "http"
                                                                            //     )
                                                                            //         ? counter.icon
                                                                            //         : `/img/${counter.icon}`
                                                                            // } 
                                                                            src={
                                                                                counter.icon
                                                                            }
                                                                            className="rounded-circle"
                                                                            width="45"
                                                                            alt={
                                                                                counter.entitle
                                                                            }
                                                                            // onError={(e) => { e.target.src = "/design/assets/new/development.gif"; }}
                                                                        />
                                                                    )}
                                                                </div>
                                                                <div className="it-nw-fun-fact-text headline pera-content">
                                                                    <h3 className="dept-font">
                                                                        {counter.numeric_type_icon && (
                                                                            <img
                                                                                src={
                                                                                    counter.numeric_type_icon
                                                                                }
                                                                                alt="numeric"
                                                                            />
                                                                        )}
                                                                        <span className="counter mt-left">
                                                                            {
                                                                                counter.counter_number
                                                                            }
                                                                        </span>
                                                                    </h3>
                                                                    <p>
                                                                        {getDisplayText(
                                                                            counter.entitle,
                                                                            counter.maltitle
                                                                        )}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    )
                                                )}

                                            {/* Always render legacy hardcoded counters as well (so old images/values still show) */}
                                            {/* {[
                                                // {
                                                //     img: "/design/assets/new/development.gif",
                                                //     value: "11.4",
                                                //     unit: "Lakh Cr",
                                                //     text: "Gross domestic product",
                                                // },
                                                // {
                                                //     img: "/design/assets/new/money.gif",
                                                //     value: "2,81,001+",
                                                //     text: "Per capita income",
                                                // },
                                                // {
                                                //     img: "/design/assets/new/growth.gif",
                                                //     value: "89,941+",
                                                //     text: "KIFFB investment",
                                                // },
                                            ].map((item, index) => (
                                                // <div
                                                //     className="col-lg-4 col-md-6"
                                                //     key={`legacy-${index}`}
                                                // >
                                                //     <div className="it-nw-fun-fact-innerbox d-flex align-items-center">
                                                //         <div className="it-nw-fun-fact-icon d-flex justify-content-center align-items-center">
                                                //             <img
                                                //                 src={item.img}
                                                //                 className="rounded-circle"
                                                //                 width="45"
                                                //                 alt="ente_keralam"
                                                //                 onError={(e) => { e.target.src = "/design/assets/new/development.gif"; }}
                                                //             />
                                                //         </div>
                                                //         <div className="it-nw-fun-fact-text headline pera-content">
                                                //             <h3 className="dept-font">
                                                //                 <img
                                                //                     src="/design/assets/new/rupee.svg"
                                                //                     alt="ente_keralam"
                                                //                 />{" "}
                                                //                 <span className="counter mt-left">
                                                //                     {item.value}
                                                //                 </span>
                                                //                 {item.unit && (
                                                //                     <small>
                                                //                         {" "}
                                                //                         {item.unit}
                                                //                     </small>
                                                //                 )}
                                                //             </h3>
                                                //             <p>{item.text}</p>
                                                //         </div>
                                                //     </div>
                                                // </div>
                                            ))} */}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {/* ================== PROGRESS SECTION ================== */}
                            <div
                                id="it-nw-service"
                                className="it-nw-service-section position-relative background"
                            >
                                <div className="container">
                                    <div className="it-nw-service-upper-wrapper">
                                        <div className="row">
                                            <div
                                                className="col-lg-12 wow fadeInLeft"
                                                data-wow-delay="0ms"
                                                data-wow-duration="1500ms"
                                            >
                                                <div className="it-nw-section-title headline pera-content">
                                                    <span className="it-nw-title-tag">
                                                        Progress
                                                    </span>
                                                    <h2>Progress made</h2>
                                                    {/* <p>
                                                        {getProgressArticles().length > 0
                                                            ? getProgressArticles()[0].endescription ||
                                                              getProgressArticles()[0].maltitle
                                                            : 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.'}
                                                    </p> */}
                                                </div>
                                            </div>

                                            {/* <div className="col-lg-6 wow fadeInRight" data-wow-delay="200ms" data-wow-duration="1500ms">
                                              <div className="it-nw-service-content">
                                                  <div className="row">
                                                      {[
                                                          { img: "/design/assets/new/dfds.jpg" },
                                                          { img: "/design/assets/new/vff.png" },
                                                      ].map((service, idx) => (
                                                          <div className="col-md-6" key={idx}>
                                                              <div className="it-nw-service-innerbox position-relative">
                                                                  <div className="it-nw-service-inner-text headline pera-content">
                                                                      <img src={service.img} alt="ente_keralam" />
                                                                      <h6>
                                                                          <a href="#">Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur,</a>
                                                                      </h6>
                                                                      <a href="#" className="rm">
                                                                          Read More <i className="fas fa-arrow-right"></i>
                                                                      </a>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      ))}
                                                  </div>
                                              </div>
                                          </div> */}
                                        </div>
                                    </div>

                                    {/* Lower grid */}
                                    <div className="it-nw-service-lower-wrapper">
                                        <div className="row">
                                            {getProgressArticles()
                                                .slice(0, 3)
                                                .map((article, idx) => (
                                                    <div
                                                        className="col-lg-4 col-md-6 wow fadeInUp"
                                                        key={article.id || idx}
                                                        data-wow-delay={`${
                                                            idx * 200
                                                        }ms`}
                                                        data-wow-duration="1500ms"
                                                    >
                                                        <div
                                                            className="it-nw-service-innerbox position-relative margin-top-20"
                                                            style={{
                                                                display: "flex",
                                                                flexDirection:
                                                                    "column",
                                                                height: "100%",
                                                            }}
                                                        >
                                                            <div
                                                                className="it-nw-service-inner-text headline pera-content"
                                                                style={{
                                                                    display:
                                                                        "flex",
                                                                    flexDirection:
                                                                        "column",
                                                                    height: "100%",
                                                                }}
                                                            >
                                                                {article.poster && (
                                                                    <img
                                                                        src={`${article.poster}`}
                                                                        alt={getDisplayText(
                                                                            article.entitle,
                                                                            article.maltitle
                                                                        )}
                                                                        // onError={(e) => {
                                                                        //     e.target.src = "/design/assets/new/dfds.jpg";
                                                                        // }}
                                                                    />
                                                                )}
                                                                <h6
                                                                    style={{
                                                                        minHeight:
                                                                            "calc(3 * 1.5em)",
                                                                        lineHeight:
                                                                            "1.5",
                                                                        marginBottom:
                                                                            "auto",
                                                                    }}
                                                                >
                                                                    <Link
                                                                        to={`/dept-detail?id=${
                                                                            article.id ||
                                                                            ""
                                                                        }`}
                                                                    >
                                                                        {getDisplayText(
                                                                            article.entitle,
                                                                            article.maltitle
                                                                        )}
                                                                    </Link>
                                                                </h6>
                                                                <Link
                                                                    to={`/dept-detail?id=${
                                                                        article.id ||
                                                                        ""
                                                                    }`}
                                                                    className="rm"
                                                                >
                                                                    Read More{" "}
                                                                    <i className="fas fa-arrow-right"></i>
                                                                </Link>
                                                            </div>
                                                        </div>
                                                    </div>
                                                ))}
                                        </div>
                                    </div>

                                    <div
                                        className="it-nw-btn text-center wow flipInX"
                                        data-wow-delay="200ms"
                                        data-wow-duration="1500ms"
                                    >
                                        <Link
                                            className="d-flex justify-content-center align-items-center"
                                            to="/dept-detail"
                                        >
                                            View More{" "}
                                            <i className="fas fa-arrow-right"></i>
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            {/* ================== BLOG SECTION ================== */}
                            {sectorSlug !== "industry" && (
                            <div
                                id="it-nw-blog"
                                className="newVert it-nw-blog-section position-relative"
                            >
                                {/* <div className="it-nw-blog-sh position-absolute">
                                  <img src="/design/assets/background/image19.png" alt="ente_keralam" />
                              </div> */}

                                <div className="floating-wrapper">
                                    <img
                                        src="/design/assets/background/win-04.svg"
                                        alt="floating"
                                        className="floating-img"
                                    />
                                </div>

                                <div className="container">
                                    <div className="it-nw-blog-top-wrap d-flex justify-content-between align-items-center">
                                        <div className="it-nw-section-title headline pera-content">
                                            <span className="it-nw-title-tag">
                                                NO:1{" "}
                                            </span>
                                            <h2>Kerala Number One</h2>
                                            {/* <p>
                                                {getKeralaNumberOneArticles()
                                                    .length > 0
                                                    ? getKeralaNumberOneArticles()[0]
                                                          .endescription ||
                                                      getKeralaNumberOneArticles()[0]
                                                          .maltitle
                                                    : "incididunt ut labore et dolore magna aliqua. Quis ipsum suspendisse ultrices gravida. Risus commodo viverra maecenas accumsan lacus vel facilisis."}
                                            </p> */}
                                        </div>
                                        <div className="it-nw-btn text-center">
                                            <a
                                                className="d-flex justify-content-center align-items-center"
                                                href="#"
                                            >
                                                View More{" "}
                                                <i className="fas fa-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>

                                    {/* <div className="it-nw-blog-content">
                                      <div className="it-nw-blog-slider">
                                          {["no1.png", "no3.png", "no2.png", "no3.png", "no1.png", "no3.png"].map((img, i) => (
                                              <div className="it-nw-blog-innerbox" key={i}>
                                                  <div className="it-nw-blog-inner-text1 headline">
                                                      <h4 className="vertH">Sample Heading</h4>
                                                  </div>
                                                  <div className="it-nw-blog-inner-img">
                                                      <img src={`/design/assets/new/${img}`} alt="ente_keralam" />
                                                  </div>
                                                  <div className="vertR">
                                                      <a href="#">Read More</a>
                                                  </div>
                                                  <div className="sicons">
                                                      <a href="#"><FaFacebookF /></a>
                                                      <a href="#"><FaInstagram /></a>
                                                      <a href="#"><FaWhatsapp /></a>
                                                      <a href="#"><FaTwitter /></a>
                                                  </div>
                                              </div>
                                          ))}
                                      </div>
                                  </div> */}
                                    <KeralaNumberOneSlider
                                        articles={getKeralaNumberOneArticles()}
                                    />
                                </div>
                            </div>
                        )}

                            {/* ================== OTHER ACHIEVEMENTS ================== */}
                            <section
                                id="app-dm-benifit"
                                className="app-dm-benifit-section position-relative"
                                style={{ backgroundColor: "#EBF5DE" }}
                            >
                                <span className="app-dm-benifit-shape2 position-absolute">
                                    <img
                                        src="/design/assets/bn-bg2.png"
                                        alt="ente_keralam"
                                    />
                                </span>
                                <div className="blog-shapes-grid-right">
                                    {Array.from({ length: 5 }).map((_, i) => {
                                        const file =
                                            shape_1[i % shape_1.length];
                                        return (
                                            <div
                                                className={`it-nw-blog-sh-bg sh${
                                                    (i % 4) + 1
                                                }`}
                                                key={i}
                                            >
                                                <img
                                                    className="imging"
                                                    src={`/design/assets/bgv/${file}`}
                                                    alt=""
                                                    style={{
                                                        width:
                                                            shapeSize_1[file] +
                                                            "px",
                                                    }}
                                                />
                                            </div>
                                        );
                                    })}
                                </div>
                                <div className="container">
                                    <div className="app-dm-benifit-content">
                                        <div className="row">
                                            <div className="col-lg-5 d-flex align-items-center">
                                                <div className="app-dm-benifit-img">
                                                    <img
                                                        src="/design/assets/Group.svg"
                                                        style={{
                                                            width: "100%",
                                                        }}
                                                        alt="ente_keralam"
                                                    />
                                                </div>
                                            </div>

                                            <div className="col-lg-7">
                                                <div className="app-dm-benifit-text">
                                                    <div className="it-nw-service-upper-wrapper">
                                                        <div className="row">
                                                            <div
                                                                className="col-lg-12 wow fadeInLeft"
                                                                data-wow-delay="0ms"
                                                                data-wow-duration="1500ms"
                                                            >
                                                                <div className="it-nw-section-title headline pera-content">
                                                                    <span className="it-nw-title-tag">
                                                                        Achievements
                                                                    </span>
                                                                    <h2>
                                                                        Other
                                                                        Achievements
                                                                    </h2>
                                                                    {/* <p>
                                                                        {getOtherAchievementsArticles()
                                                                            .length >
                                                                        0
                                                                            ? getOtherAchievementsArticles()[0]
                                                                                  .endescription ||
                                                                              getOtherAchievementsArticles()[0]
                                                                                  .maltitle
                                                                            : "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua."}
                                                                    </p> */}
                                                                </div>
                                                            </div>

                                                            {/* <div className="col-lg-6 wow fadeInRight" data-wow-delay="200ms" data-wow-duration="1500ms">
                                              <div className="it-nw-service-content">
                                                  <div className="row">
                                                      {[
                                                          { img: "/design/assets/new/dfds.jpg" },
                                                          { img: "/design/assets/new/vff.png" },
                                                      ].map((service, idx) => (
                                                          <div className="col-md-6" key={idx}>
                                                              <div className="it-nw-service-innerbox position-relative">
                                                                  <div className="it-nw-service-inner-text headline pera-content">
                                                                      <img src={service.img} alt="ente_keralam" />
                                                                      <h6>
                                                                          <a href="#">Lorem ipsum dolor sit amet consectetur adipisicing elit. Aspernatur,</a>
                                                                      </h6>
                                                                      <a href="#" className="rm">
                                                                          Read More <i className="fas fa-arrow-right"></i>
                                                                      </a>
                                                                  </div>
                                                              </div>
                                                          </div>
                                                      ))}
                                                  </div>
                                              </div>
                                          </div> */}
                                                        </div>
                                                    </div>

                                                    {/* <div className="it-nw-section-title headline pera-content">
                                                      <h2 className="pol">
                                                          Other<br /><span className="ml-0 surv"> Achievements</span>
                                                      </h2>
                                                  </div> */}
                                                    <br />
                                                    <div className="app-dm-benifit-tab-area">
                                                        <div className="tab-content">
                                                            <div
                                                                id="monday"
                                                                className="tab-pane fade active show"
                                                            >
                                                                <div className="row">
                                                                    {/* {getOtherAchievementsArticles().slice(0, 6).map( */}
                                                                    {getOtherAchievementsArticles().map(
                                                                        (
                                                                            article,
                                                                            num
                                                                        ) => (
                                                                            <div
                                                                                className="col-lg-6"
                                                                                key={
                                                                                    article.id ||
                                                                                    num
                                                                                }
                                                                            >
                                                                                <div className="apldg-blog-column mb-4">
                                                                                    <Link
                                                                                        to={`/dept-detail?id=${
                                                                                            article.id ||
                                                                                            ""
                                                                                        }`}
                                                                                    >
                                                                                        <div className="apldg-img-wrapper">
                                                                                            <img
                                                                                                src={`${article.poster}`}
                                                                                                alt={
                                                                                                    article.entitle
                                                                                                }
                                                                                            />
                                                                                            <div className="overlay1">
                                                                                                <button className="center-button">
                                                                                                    Read
                                                                                                    More
                                                                                                </button>
                                                                                            </div>
                                                                                        </div>
                                                                                    </Link>
                                                                                    {/* <div className="apldg-headline">
                                                                                        <Link
                                                                                            to={`/dept-detail?id=${
                                                                                                article.id ||
                                                                                                ""
                                                                                            }`}
                                                                                        >
                                                                                            <h6>
                                                                                                {article.maltitle ||
                                                                                                    article.entitle}
                                                                                            </h6>
                                                                                        </Link>
                                                                                    </div> */}
                                                                                </div>
                                                                            </div>
                                                                        )
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

                            {/* ================== AWARDS SECTION ================== */}
                            <div className="awardx position-relative">
                                <div className="blog-shapes-grid-right">
                                    {Array.from({ length: 20 }).map((_, i) => {
                                        const file = shape[i % shape.length];
                                        return (
                                            <div
                                                className={`it-nw-blog-sh-bg sh${
                                                    (i % 4) + 1
                                                }`}
                                                key={i}
                                            >
                                                <img
                                                    className="imging"
                                                    src={`/design/assets/bgv/${file}`}
                                                    alt=""
                                                    style={{
                                                        width:
                                                            shapeSize[file] +
                                                            "px",
                                                    }}
                                                />
                                            </div>
                                        );
                                    })}
                                </div>
                                <div className="container">
                                    <div className="it-nw-blog-top-wrap d-flex justify-content-between align-items-center">
                                        <div className="it-nw-section-title headline pera-content">
                                            <span className="it-nw-title-tag">
                                                Award
                                            </span>
                                            <h2>Awards & Recognition</h2>
                                        </div>
                                        {/* <div className="floating-wrapper">
                                            <img
                                                src="/design/assets/bgv/Vector (2).svg"
                                                alt="floating"
                                                className="floating-img"
                                            />
                                        </div> */}
                                    </div>

                                    <div className="row mt-4">
                                        {getAwardsArticles()
                                            .slice(0, 3)
                                            .map((article, i) => (
                                                <div
                                                    className="col-md-4"
                                                    key={article.id || i}
                                                >
                                                    <div className="card border-0 award-card">
                                                        <img
                                                            src={`/${article.poster}`}
                                                            alt={
                                                                article.entitle
                                                            }
                                                            className="mx-auto d-block"
                                                            onError={(e) => {
                                                                e.target.src =
                                                                    "/design/assets/new/2344941_144.jpg";
                                                            }}
                                                        />
                                                        <h4>
                                                            {article.maltitle ||
                                                                article.entitle}
                                                        </h4>
                                                    </div>
                                                </div>
                                            ))}
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

export default EconomySection;
