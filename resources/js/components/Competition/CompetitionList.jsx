"use client";
import { useEffect, useState } from "react";
import Select from "react-select";
import { Link } from "react-router-dom";
import axios from "axios";
import { useLanguage } from "../LanguageContext";

export default function CompetitionListingSection() {
    const { language } = useLanguage();
    const [filter, setFilter] = useState("all");
    const [search, setSearch] = useState("");
    const [sortBy, setSortBy] = useState("");
    const [items, setItems] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === 'ml' ? (malText || enText) : enText;
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

    useEffect(() => {
        const fetchContests = async () => {
            try {
                setLoading(true);
                const response = await axios.get(
                    "/api/contests"
                );

                const contestList =
                    response.data.data && Array.isArray(response.data.data)
                        ? response.data.data
                        : [];

                const formatted = contestList.map((item) => ({
                    id: item.contest_id,
                    slug: item.slug,
                    title: item.title,
                    about: item.description,
                    banner: item.banner
                        ? item.banner
                        : "/design/assets/fp/Poll - 1.jpg",
                    type: item.type,
                    status:
                        new Date(item.end_date) > new Date()
                            ? "open"
                            : "closed",
                    date: item.end_date,
                    lang: "ml",
                }));

                setItems(formatted);
            } catch (err) {
                console.error(err);
                setError("Failed to load contests");
            } finally {
                setLoading(false);
            }
        };

        fetchContests();
    }, []);

    const sortByOptions = [
        { value: "newest", label: "Newest First" },
        { value: "oldest", label: "Oldest First" },
        { value: "popular", label: "Most Popular" },
        { value: "result", label: "Result" },
    ];

    const filteredItems = items.filter((item) => {
        const matchStatus = filter === "all" ? true : item.status === filter;
        const matchSearch = item.title
            .toLowerCase()
            .includes(search.toLowerCase());
        return matchStatus && matchSearch;
    });

    return (
        <>
            <section id="saasio-breadcurmb" className="saasio-breadcurmb-section">
                <div className="container-fluid">
                    <div className="col-md-11 mx-auto">
                        <div className="breadcurmb-title">
                            <h2>Competition</h2>
                        </div>
                        <div className="breadcurmb-item-list ul-li">
                            <ul className="saasio-page-breadcurmb">
                                <li>
                                    <a href="/">Home</a>
                                </li>
                                <li>
                                    <a href="#">Competition</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            <div className="main-sec">
                <section className="listing apldg-blog-section">
                    <div className="blog-shapes-grid">
                        {Array.from({ length: 200 }).map((_, i) => {
                            const file = shapes[i % shapes.length];
                            return (
                                <div
                                    className={`it-nw-blog-sh-bg sh${(i % 3) + 1}`}
                                    key={i}
                                >
                                    <img
                                        src={`/design/assets/bgv/${file}`}
                                        alt=""
                                        style={{ width: shapeSizes[file] + "px" }}
                                    />
                                </div>
                            );
                        })}
                    </div>

                    <div
                        className="container-fluid"
                        style={{ zIndex: 6, position: "relative" }}
                    >
                        <div className="col-md-11 mx-auto">
                            <div className="row top-search1">
                                <div className="col-lg-6 col-md-4 col-12">
                                    <div className="d-flex align-items-center margin-search">
                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                name="status"
                                                checked={filter === "all"}
                                                onChange={() => setFilter("all")}
                                            />
                                            <label className="form-check-label color">
                                                All
                                            </label>
                                        </div>

                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                name="status"
                                                checked={filter === "open"}
                                                onChange={() => setFilter("open")}
                                            />
                                            <label className="form-check-label color">
                                                Open
                                            </label>
                                        </div>

                                        <div className="form-check mr-2">
                                            <input
                                                type="radio"
                                                className="form-check-input"
                                                name="status"
                                                checked={filter === "closed"}
                                                onChange={() =>
                                                    setFilter("closed")
                                                }
                                            />
                                            <label className="form-check-label color">
                                                Closed
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div className="col-lg-3 col-md-4 col-12">
                                    <form
                                        className="relative-position search-bx"
                                        onSubmit={(e) => e.preventDefault()}
                                    >
                                        <input
                                            type="text"
                                            className="form-control"
                                            placeholder="Search..."
                                            value={search}
                                            onChange={(e) =>
                                                setSearch(e.target.value)
                                            }
                                        />
                                        <button type="submit">
                                            <i className="fas fa-search"></i>
                                        </button>
                                    </form>
                                </div>

                                <div className="col-lg-3 col-md-4 col-12">
                                    <Select
                                        value={sortByOptions.find(
                                            (o) => o.value === sortBy
                                        )}
                                        onChange={(option) =>
                                            setSortBy(option.value)
                                        }
                                        options={sortByOptions}
                                        placeholder="Sort By"
                                        classNamePrefix="react-select"
                                    />
                                </div>
                            </div>

                            <hr className="mb-0" />

                            <div className="">
                                <div className="apldg-blog-right  wow fadeInRight">
                                    <div className="row">
                                        {filteredItems.map((item) => (
                                            <div
                                                key={item.id}
                                                className="col-md-4 col-lg-3"
                                            >
                                                <div
                                                    className={`apldg-blog-column ${item.status}`}
                                                >
                                                    <a href="#">
                                                        <div className="apldg-img-wrapper">
                                                            <img
                                                                src={item.banner}
                                                                alt={item.title}
                                                            />
                                                        </div>
                                                        <div className="statusShar">
                                                            <div className="stat">
                                                                <p>
                                                                    Status{" "}
                                                                    <span>
                                                                        {item.status ===
                                                                        "open"
                                                                            ? "Active"
                                                                            : "Closed"}
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>

                                                        <div className="apldg-headline">
                                                            <h5>{item.title}</h5>
                                                        </div>

                                                        <div className="apldg-blog-meta">
                                                            <span className="apldg-blog-date">
                                                                Last date :{" "}
                                                                {item.date}
                                                            </span>
                                                        </div>
                                                    </a>

                                                    <div className="linkWrap mt-2">
                                                        <Link
                                                            to={`/competition-details/${item.slug}`}
                                                        >
                                                            <button className="linkz style-7">
                                                                <span
                                                                    className="circle"
                                                                    aria-hidden="true"
                                                                >
                                                                    <span className="icon arrow"></span>
                                                                </span>
                                                                <span className="button-text">
                                                                    Participate Now
                                                                </span>
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
                </section>
            </div>
        </>
    );
}
