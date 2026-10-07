"use client";
import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { pledgeAPI } from "../../services/api";
import { useLanguage } from "../LanguageContext";



export default function PledgeListingSection() {
    const { language } = useLanguage();
    const [filter, setFilter] = useState("all");
    const [department, setDepartment] = useState("");
    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === 'ml' ? (malText || enText) : enText;
    };

    useEffect(() => {
        const fetchPledges = async () => {
            try {
                setLoading(true);
                const response = await pledgeAPI.getAll();
                console.log(response.data, "json1111");

    if (response?.data) {
      const pledgesData = response.data.pledges || response.data.data || response.data;

      // Ensure we always work with an array
      const pledges = Array.isArray(pledgesData) ? pledgesData : [pledgesData];

      const formattedPledges = pledges.map((pledge, index) => ({
        id: pledge.pledge_id || pledge.id,
        status: new Date(pledge.end_date) > new Date() ? "open" : "closed",
        image: pledge.poster
          ? `${pledge.poster}`
          : `/design/assets/fp/Pledge - ${index + 1}.jpg`,
        title: pledge.title || pledge.topic || pledge.name,
        date: pledge.end_date,
        about: pledge.about || pledge.description,
        lang: "ml",
      }));

      setItems(formattedPledges);
      console.log(formattedPledges, "formattedPledges");
    }
  } catch (err) {
    console.error("Error fetching pledges:", err);
    setError("Failed to load pledges");
  } finally {
    setLoading(false);
  }
};

    fetchPledges();
}, []);
    const filteredItems = items.filter((item) =>
        filter === "all" ? true : item.status === filter
    );

    return (
        <><section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
            <div className="container-fluid">
                <div className="col-md-11 mx-auto">
                    <div className="breadcurmb-title">
                        <h2>Pledge</h2>
                    </div>
                    <div className="breadcurmb-item-list ul-li">
                        <ul className="saasio-page-breadcurmb">
                            <li>
                                <a href="/">Home</a>
                            </li>
                            <li>
                                <a href="#">Pledge</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {/* Background Video (optional) */}
            {/*
    <video className="background-video" autoPlay muted loop>
      <source
        src="/img/6635696_Tourism_Shadow_1920x1080.mp4"
        type="video/mp4"
      />
    </video>
    */}
        </section><div className="main-sec">
                <section className="listing bg-white apldg-blog-section">
                    <div className="it-nw-side-bg-left text-center position-absolute">
          <img src="/design/assets/background/sides.svg" alt="" />
        </div>
                    <div className="container-fluid" style={{ zIndex: 6, position: "relative" }}>
                        {/* Top Filters */}
                        <div className="col-md-11 mx-auto">
                            <div className="row top-search1">
                                <div className="col-lg-9 col-md-8 col-12">
                                    <div className="d-flex align-items-center">
                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                id="pledge-status-all"
                                                name="status"
                                                checked={filter === "all"}
                                                onChange={() => setFilter("all")} />
                                            <label className="form-check-label color" htmlFor="pledge-status-all">All</label>
                                        </div>
                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                id="pledge-status-open"
                                                name="status"
                                                checked={filter === "open"}
                                                onChange={() => setFilter("open")} />
                                            <label className="form-check-label color" htmlFor="pledge-status-open">Open</label>
                                        </div>
                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                id="pledge-status-closed"
                                                name="status"
                                                checked={filter === "closed"}
                                                onChange={() => setFilter("closed")} />
                                            <label className="form-check-label color" htmlFor="pledge-status-closed">Closed</label>
                                        </div>
                                    </div>
                                </div>

                                <div className="col-lg-3 col-md-4 col-12">
                                    <form className="relative-position search-bx">
                                        <select
                                            className="form-select"
                                            value={department}
                                            onChange={(e) => setDepartment(e.target.value)}
                                        >
                                            <option value="">Select department</option>
                                            <option value="1">One</option>
                                            <option value="2">Two</option>
                                            <option value="3">Three</option>
                                        </select>
                                        <button type="submit">
                                            <i className="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <hr className="mb-0" />

                            {/* Cards */}
                            <div className="">
                                <div className="apldg-blog-right  wow fadeInRight">
                                <div className="row">
                                    {filteredItems.map((item) => (
                                        <div key={item.id} className="col-md-4 col-lg-3">
                                            <div className={`apldg-blog-column ${item.status}`}>
                                                <a href="#">
                                                    <div className="apldg-img-wrapper">
                                                        <img src={item.image} alt={item.title} />
                                                    </div>
                                                    <div className="statusShar">
                                                        <div className="stat">
                                                            <p>
                                                                Status <span>{item.status === "open" ? "Active" : "Closed"}</span>
                                                            </p>
                                                        </div>
                                                        <div className="sheir ">
                                                            <svg height="22" className="fb0" viewBox="0 0 176 176" width="22" xmlns="http://www.w3.org/2000/svg"><g id="Layer_2" data-name="Layer 2"><g id="_01.facebook" data-name="01.facebook"><path id="icon" d="m88 0a88 88 0 1 0 88 88 88 88 0 0 0 -88-88zm27.88 77.59-1.77 15.32a2.86 2.86 0 0 1 -2.82 2.57h-16l-.08 45.45a2.05 2.05 0 0 1 -2 2.07h-16.21a2 2 0 0 1 -2-2.08v-45.44h-12a2.87 2.87 0 0 1 -2.84-2.9l-.06-15.33a2.88 2.88 0 0 1 2.84-2.92h12.06v-14.8c0-17.18 10.2-26.53 25.16-26.53h12.26a2.88 2.88 0 0 1 2.85 2.92v12.91a2.88 2.88 0 0 1 -2.85 2.92h-7.52c-8.13 0-9.71 4-9.71 9.77v12.81h17.87a2.89 2.89 0 0 1 2.82 3.26z" /></g></g></svg>
                                                            <svg height="22" className="inta0" viewBox="0 0 512 512" width="22" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1"><path d="m256 200.0165c34.62 0 62.6843 27.6308 62.6843 61.7148 0 34.0771-28.0645 61.708-62.6843 61.708s-62.6847-27.6309-62.6847-61.708c0-34.084 28.065-61.7148 62.6847-61.7148zm0-24.6778c-48.4676 0-87.7585 38.6778-87.7585 86.3926 0 47.708 39.2909 86.3926 87.7585 86.3926s87.7581-38.6846 87.7581-86.3926c0-47.7148-39.29-86.3926-87.7581-86.3926zm90.4681-20.7676a24.6876 24.6876 0 1 0 25.0742 24.6846 24.8828 24.8828 0 0 0 -25.0742-24.6846zm-151.7256-26.8652h122.515c39.8565 0 72.167 31.8076 72.167 71.0391v125.9658c0 39.2383-32.3105 71.0391-72.167 71.0391h-122.515c-39.8565 0-72.167-31.8008-72.167-71.0391v-125.9658c0-39.2315 32.31-71.0391 72.167-71.0391zm-11.5548-24.5684c-47.3748 0-85.78 37.81-85.78 84.4444v148.292c0 46.6416 38.4047 84.4443 85.78 84.4443h145.6251c47.3743 0 85.779-37.8027 85.779-84.4443v-148.292c0-46.6348-38.4047-84.4444-85.779-84.4444zm72.8123-89.2089c136.8563 0 247.8 110.94 247.8 247.8027 0 136.8555-110.9435 247.7959-247.8 247.7959s-247.8-110.9404-247.8-247.7959c0-136.8623 110.9435-247.8027 247.8-247.8027z" fill-rule="evenodd" /></svg>
                                                        </div>
                                                    </div>
                                                    <div className="apldg-headline">
                                                        <h5>{item.title}</h5>
                                                    </div>
                                                    <div className="apldg-blog-meta">
                                                        <span className="apldg-blog-date">Last date : {item.date}</span>
                                                    </div>
                                                </a>
                                                <div className="linkWrap mt-2">
                                                    <Link to={`/pledge/${item.id}`}>
                                                        <button className="linkz style-7">
                                                            <span className="circle" aria-hidden="true">
                                                                <span className="icon arrow"></span>
                                                            </span>
                                                            <span className="button-text">Pledge Now</span>
                                                        </button>
                                                    </Link>
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                            </div>
                    </div>
            </div>
        </section >
          </div ></>
  );
}
