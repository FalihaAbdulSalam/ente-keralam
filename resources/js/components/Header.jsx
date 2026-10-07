"use client";
import { useState, useEffect, useRef } from "react";
import { useNavigate } from "react-router-dom";
import { FaChevronUp } from "react-icons/fa";
import { useAuth } from "./App";
import { useLanguage } from "./LanguageContext";
import dashboardAPI from "../services/dashboardAPI";
import UserAvatar from "./UserAvatar";
import { usePageType } from "../hooks/usePageType";
import MorphingPreloader from "./MorphingPreloader";
import { menusAPI } from "../services/api";

export default function Header() {
    const { user, logout } = useAuth();
    const { language, toggleLanguage } = useLanguage();
    const navigate = useNavigate();
    const pageType = usePageType();
    const [loading, setLoading] = useState(true);
    const [menuOpen, setMenuOpen] = useState(false);
    const [showProfileMenu, setShowProfileMenu] = useState(false);
    const [profileCompletion, setProfileCompletion] = useState(null);
    const profileRef = useRef(null);
    const [menus, setMenus] = useState([]);
    const [searchOpen, setSearchOpen] = useState(false);
    const [openSubmenus, setOpenSubmenus] = useState({});
    const [searchTerm, setSearchTerm] = useState("");

    // Handle loading state - simple timeout approach that worked before
    useEffect(() => {
        const timer = setTimeout(() => {
            setLoading(false);
        }, 1500); // 1.5 seconds - enough time for content to load

        return () => clearTimeout(timer);
    }, []);

    // Fetch profile completion when user is logged in
    useEffect(() => {
        if (user) {
            fetchProfileCompletion();
        }
    }, [user]);

    // Fetch menus from API
    useEffect(() => {
        async function fetchMenus() {
            try {
                const { data } = await menusAPI.getAll();
                if (data?.status && Array.isArray(data.data)) {
                    setMenus(
                        data.data.sort(
                            (a, b) => (a.order || 0) - (b.order || 0)
                        )
                    );
                }
            } catch (e) {
                // Fallback will be static if call fails
                // console.error('Failed to load menus', e);
            }
        }
        fetchMenus();
    }, []);

    const fetchProfileCompletion = async () => {
        try {
            const response = await dashboardAPI.getProfileCompletion();
            setProfileCompletion(response.data);
        } catch (error) {
            console.error("Failed to fetch profile completion:", error);
        }
    };

    // Toggle menu visibility
    const toggleMobileMenu = () => {
        setMenuOpen(!menuOpen);
    };

    // Toggle profile menu
    const toggleProfileMenu = () => {
        setShowProfileMenu((s) => !s);
    };

    // Toggle submenu (used for mobile)
    const toggleSubmenu = (menuId) => {
        setOpenSubmenus((prev) => ({
            ...prev,
            [menuId]: !prev[menuId],
        }));
    };

    // Prevent navigation when a menu has submenus; instead, toggle
    const handleMenuClick = (e, menu) => {
        if (menu?.submenus?.length) {
            e.preventDefault();
            // On mobile, explicitly toggle to reveal submenu
            if (typeof window !== "undefined" && window.innerWidth <= 991) {
                toggleSubmenu(menu.id);
            }
        }
    };

    // Logout handler
    const handleLogout = async () => {
        setShowProfileMenu(false);
        await logout();
        window.location.href = "/";
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const term = searchTerm.trim();
        if (!term) return;
        setSearchOpen(false);
        navigate(`/search?keyword=${encodeURIComponent(term)}`);
    };

    // Close profile menu on outside click
    useEffect(() => {
        if (window.location.pathname === "/dashboard" && profileRef.current) {
            // Hide the profile by setting its display to 'none'
            profileRef.current.style.display = "none";
        } else if (profileRef.current) {
            // Show the profile if we're not on the /dashboard page
            profileRef.current.style.display = "block";
        }

        function handleOutsideClick(e) {
            if (profileRef.current && !profileRef.current.contains(e.target)) {
                setShowProfileMenu(false);
            }
        }
        document.addEventListener("mousedown", handleOutsideClick);
        return () =>
            document.removeEventListener("mousedown", handleOutsideClick);
    }, []);

    return (
        <>
            {/* Preloader with blur effect - content loads underneath */}
            {loading && <MorphingPreloader type={pageType} />}
            <div className="up">
                <a href="#" className="scrollup text-center">
                    <i className="fas fa-chevron-up"></i>
                </a>
            </div>
            <div
                className={`apldg-header-form ${
                    searchOpen ? "apldg-form-open" : ""
                }`}
            >
                <div
                    className="apldg-form-overlay"
                    onClick={() => setSearchOpen(false)}
                ></div>

                <form onSubmit={handleSearch}>
                    <input
                        type="text"
                        placeholder="Search..."
                        value={searchTerm}
                        onChange={(e) => setSearchTerm(e.target.value)}
                    />
                    <button type="submit">Go</button>
                </form>
            </div>

            <div className="up">
                <a href="#" className="scrollup text-center">
                    <FaChevronUp />
                </a>
            </div>

            {/* Header Section */}
            <header id="it-nw-header" className="it-nw-header-area">
                <div className="container-top">
                    {/* --- Top Bar --- */}
                    <div className="it-nw-header-top-content d-flex justify-content-between">
                        <div className="it-nw-header-cta-social d-flex">
                            <div className="it-nw-header-cta ul-li">
                                <ul>
                                    <li>
                                        <img
                                            src="/design/assets/new/loW.svg"
                                            alt=""
                                        />
                                        <span className="govt">
                                            GOVERNMENT OF KERALA
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div className="it-nw-header-login ul-li d-flex">
                            <ul className="shar">
                                <li>
                                    <a
                                        type="button"
                                        onClick={() => toggleLanguage("ml")}
                                        style={{
                                            background: "none",
                                            border: "none",
                                            cursor: "pointer",
                                            padding: 0,
                                        }}
                                    >
                                        <span
                                            style={{
                                                fontWeight:
                                                    language === "ml"
                                                        ? "bold"
                                                        : "normal",
                                                opacity:
                                                    language === "ml" ? 1 : 0.6,
                                                transition: "all 0.2s ease",
                                            }}
                                        >
                                            മലയാളം
                                        </span>
                                    </a>
                                </li>
                                <li style={{ marginLeft: "10px" }}>
                                    <a
                                        type="button"
                                        onClick={() => toggleLanguage("en")}
                                        style={{
                                            background: "none",
                                            border: "none",
                                            cursor: "pointer",
                                            padding: 0,
                                        }}
                                    >
                                        <span
                                            style={{
                                                fontWeight:
                                                    language === "en"
                                                        ? "bold"
                                                        : "normal",
                                                opacity:
                                                    language === "en" ? 1 : 0.6,
                                                transition: "all 0.2s ease",
                                            }}
                                        >
                                            English
                                        </span>
                                    </a>
                                </li>
                                {/* <li className="dropdown">
                  <img src="/design/assets/share.png" alt="" width="20" />
                  <ul className="dropdown-content">
                    <li>
                      <a href="#">
                        <img src="/design/assets/youtube.png" alt="" />
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <img src="/design/assets/facebook.png" alt="" />
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <img src="/design/assets/twitter.png" alt="" />
                      </a>
                    </li>
                    <li>
                      <a href="#">
                        <img src="/design/assets/instagram.png" alt="" />
                      </a>
                    </li>
                  </ul>
                </li> */}
                            </ul>
                        </div>
                    </div>

                    {/* --- Main Header --- */}
                    <div className="it-nw-header-main d-flex justify-content-between align-items-center">
                        <div className="it-nw-header-logo">
                            <a href="/">
                                <img
                                    src="/design/assets/new/enteK.svg"
                                    alt="Logo"
                                />
                            </a>
                        </div>

                        <div className="it-nw-main-menu-wrap d-flex align-items-center w-100 justify-content-end">
                            <nav className="it-nw-main-navigation ul-li">
                                <ul
                                    id="main-nav"
                                    className="navbar-nav text-capitalize clearfix"
                                >
                                    {menus && menus.length > 0 ? (
                                        menus.map((m) => (
                                            <li
                                                key={m.id}
                                                className={
                                                    m?.submenus?.length
                                                        ? `dropdown ${
                                                              openSubmenus[m.id]
                                                                  ? "submenu-open"
                                                                  : ""
                                                          }`
                                                        : undefined
                                                }
                                            >
                                                {/* const isSubmenuOpen = !!openSubmenus[m.id]; */}
                                                <a
                                                    href={
                                                        m.slug
                                                            ? `/${m.slug}`
                                                            : "/"
                                                    }
                                                    data-lang={
                                                        language === "ml" &&
                                                        /\u0D00-\u0D7F/.test(
                                                            m.maltitle
                                                        )
                                                            ? "ml"
                                                            : undefined
                                                    }
                                                    onClick={(e) =>
                                                        handleMenuClick(e, m)
                                                    }
                                                >
                                                    {language === "ml"
                                                        ? m.maltitle ||
                                                          m.entitle
                                                        : m.entitle}
                                                </a>
                                                {m?.submenus?.length ? (
                                                    <ul
                                                        className={`dropdown-menu clearfix ${
                                                            openSubmenus[m.id]
                                                                ? "submenu-open"
                                                                : ""
                                                        }`}
                                                    >
                                                        {m.submenus
                                                            .sort(
                                                                (a, b) =>
                                                                    (a.order ||
                                                                        0) -
                                                                    (b.order ||
                                                                        0)
                                                            )
                                                            .map((s) => (
                                                                <li key={s.id}>
                                                                    <a
                                                                        href={`/${s.slug}`}
                                                                    >
                                                                        {language ===
                                                                        "ml"
                                                                            ? s.maltitle ||
                                                                              s.entitle
                                                                            : s.entitle}
                                                                    </a>
                                                                </li>
                                                            ))}
                                                    </ul>
                                                ) : null}
                                            </li>
                                        ))
                                    ) : (
                                        <>
                                            <li>
                                                <a href="/">Home</a>
                                            </li>
                                            {/* <li>
                                                <a href="/insight">Insights</a>
                                            </li> */}
                                            <li>
                                                <a href="/faq">FAQ</a>
                                            </li>
                                        </>
                                    )}

                                    {/* {user && (
                                        <li>
                                            <a href="/dashboard">My Activity</a>
                                        </li>
                                    )} */}

                                    <li>
                                        <button
                                            type="button"
                                            className="apldg-search-btn"
                                            onClick={() => setSearchOpen(true)}
                                        >
                                            <i className="fas fa-search"></i>
                                        </button>
                                    </li>
                                </ul>
                            </nav>

                            <div className="d-flex align-items-center">
                                {!user ? (
                                    // Show Login button when NOT logged in
                                    <div className="it-nw-btn text-center">
                                        <a
                                            className="d-flex justify-content-center align-items-center"
                                            href="/login"
                                        >
                                            Login
                                            <i className="fas fa-arrow-right"></i>
                                        </a>
                                    </div>
                                ) : (
                                    // Show Profile avatar when logged in
                                    <div ref={profileRef}>
                                        <style>{`
                                            @keyframes electricBorder1 {
                                                0% { stroke-dashoffset: 200; opacity: 1; }
                                                50% { stroke-dashoffset: 0; opacity: 1; }
                                                75% { stroke-dashoffset: 0; opacity: 0.5; }
                                                100% { stroke-dashoffset: 0; opacity: 0; }
                                            }
                                            @keyframes electricBorder2 {
                                                0% { stroke-dashoffset: 250; opacity: 1; }
                                                50% { stroke-dashoffset: 50; opacity: 1; }
                                                75% { stroke-dashoffset: 50; opacity: 0.5; }
                                                100% { stroke-dashoffset: 50; opacity: 0; }
                                            }
                                            @keyframes electricBorder3 {
                                                0% { stroke-dashoffset: 300; opacity: 1; }
                                                50% { stroke-dashoffset: 100; opacity: 1; }
                                                75% { stroke-dashoffset: 100; opacity: 0.5; }
                                                100% { stroke-dashoffset: 100; opacity: 0; }
                                            }
                                            @keyframes electricBorder4 {
                                                0% { stroke-dashoffset: 350; opacity: 1; }
                                                50% { stroke-dashoffset: 150; opacity: 1; }
                                                75% { stroke-dashoffset: 150; opacity: 0.5; }
                                                100% { stroke-dashoffset: 150; opacity: 0; }
                                            }
                                            .profile-avatar-container {
                                                position: relative;
                                                width: 54px;
                                                height: 54px;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                            }
                                            .profile-avatar-ring {
                                                position: absolute;
                                                top: 0;
                                                left: 0;
                                                width: 100%;
                                                height: 100%;
                                                z-index: 1;
                                                pointer-events: none;
                                            }
                                            .profile-avatar-ring svg {
                                                width: 100%;
                                                height: 100%;
                                            }
                                            .profile-avatar-ring circle {
                                                fill: none;
                                                stroke-width: 3;
                                                stroke-dasharray: 40, 160;
                                                stroke-linecap: round;
                                                opacity: 0;
                                            }
                                            .profile-avatar-ring circle:nth-child(1) {
                                                stroke: #09f;
                                                animation: electricBorder1 2s ease-out 1.5s 1 forwards;
                                            }
                                            .profile-avatar-ring circle:nth-child(2) {
                                                stroke: #3c0;
                                                animation: electricBorder2 2s ease-out 1.5s 1 forwards;
                                            }
                                            .profile-avatar-ring circle:nth-child(3) {
                                                stroke: #f09;
                                                animation: electricBorder3 2s ease-out 1.5s 1 forwards;
                                            }
                                            .profile-avatar-ring circle:nth-child(4) {
                                                stroke: #f33;
                                                animation: electricBorder4 2s ease-out 1.5s 1 forwards;
                                            }
                                            .profile-avatar-inner {
                                                position: relative;
                                                width: 48px;
                                                height: 48px;
                                                border-radius: 50%;
                                                background: white;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                z-index: 2;
                                            }
                                        `}</style>
                                        <div className="it-nw-btn text-center margin-reduce">
                                            <div className="profile-avatar-container ml-3">
                                                <div className="profile-avatar-ring">
                                                    <svg viewBox="0 0 54 54">
                                                        <circle
                                                            cx="27"
                                                            cy="27"
                                                            r="25"
                                                        />
                                                        <circle
                                                            cx="27"
                                                            cy="27"
                                                            r="25"
                                                        />
                                                        <circle
                                                            cx="27"
                                                            cy="27"
                                                            r="25"
                                                        />
                                                        <circle
                                                            cx="27"
                                                            cy="27"
                                                            r="25"
                                                        />
                                                    </svg>
                                                </div>
                                                <div className="profile-avatar-inner">
                                                    <a
                                                        className="d-flex justify-content-center align-items-center user-profile"
                                                        onClick={
                                                            toggleProfileMenu
                                                        }
                                                        aria-haspopup="true"
                                                        aria-expanded={
                                                            showProfileMenu
                                                        }
                                                        style={{
                                                            cursor: "pointer",
                                                            position:
                                                                "relative",
                                                            width: "100%",
                                                            height: "100%",
                                                            display: "flex",
                                                            alignItems:
                                                                "center",
                                                            justifyContent:
                                                                "center",
                                                        }}
                                                    >
                                                        {!user?.avatar &&
                                                        !user?.avatar_url ? (
                                                            <img
                                                                src="/design/assets/background/user-circles-set.png"
                                                                alt=""
                                                            />
                                                        ) : (
                                                            <UserAvatar
                                                                user={user}
                                                                size={40}
                                                                fontSize={18}
                                                                className="profile-avatar"
                                                            />
                                                        )}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        {showProfileMenu && (
                                            <div className="profile-menu">
                                                <div className="profile-greeting">
                                                    Hi {user.name}!
                                                </div>

                                                {/* Profile Completion Tracker */}
                                                {profileCompletion &&
                                                    !profileCompletion.is_complete &&
                                                    profileCompletion.percentage <
                                                        100 && (
                                                        <div className="profile-completion-tracker">
                                                            <div className="completion-text">
                                                                <small>
                                                                    Profile:{" "}
                                                                    {
                                                                        profileCompletion.percentage
                                                                    }
                                                                    % complete
                                                                </small>
                                                                <small className="completion-reward">
                                                                    +10 points
                                                                    on
                                                                    completion!
                                                                </small>
                                                            </div>
                                                            <div className="completion-bar">
                                                                <div
                                                                    className="completion-progress"
                                                                    style={{
                                                                        width: `${profileCompletion.percentage}%`,
                                                                    }}
                                                                ></div>
                                                            </div>
                                                        </div>
                                                    )}

                                                <hr
                                                    style={{ margin: "6px 0" }}
                                                />
                                                <ul className="profile-menu-list">
                                                    <li className="profile-menu-item">
                                                        <a href="/profile/edit">
                                                            Profile
                                                        </a>
                                                    </li>
                                                    <li className="profile-menu-item">
                                                        <a href="/dashboard">
                                                            Activity
                                                        </a>
                                                    </li>
                                                </ul>
                                                <hr
                                                    style={{ margin: "8px 0" }}
                                                />
                                                <button
                                                    className="profile-logout-btn"
                                                    onClick={handleLogout}
                                                >
                                                    Logout
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                )}
                                {/* Profile button + popup */}
                                {/* <div
                  className="profile-wrapper"
                  ref={profileRef}
                  style={{ position: "relative", marginLeft: 12 }}
                >
                  <button
                    type="button"
                    className="profile-btn d-flex align-items-center"
                    onClick={toggleProfileMenu}
                    aria-haspopup="true"
                    aria-expanded={showProfileMenu}
                    style={{
                      background: "transparent",
                      border: "none",
                      cursor: "pointer",
                      padding: 0,
                    }}
                  >
                    <UserAvatar 
                      user={user} 
                      size={36} 
                      fontSize={16}
                      className=""
                    />
                  </button>

                  {showProfileMenu && (
                    <div
                      className="profile-menu"
                      style={{
                        position: "absolute",
                        // right: 0,
                        // top: 48,
                        right: "225px",
                        top: "113px",

                        background: "#fff",
                        boxShadow: "0 6px 18px rgba(0,0,0,0.1)",
                        padding: 12,
                        borderRadius: 8,
                        minWidth: 180,
                        zIndex: 1200,
                      }}
                    >
                      <div style={{ fontWeight: 600, paddingBottom: 6 }}>
                        Hi Joseph Kuruvila
                      </div>
                      <hr style={{ margin: "6px 0" }} />
                      <ul style={{ listStyle: "none", padding: 0, margin: 0 }}>
                        <li style={{ padding: "6px 0" }}>
                          <a href="/dashboard">Profile</a>
                        </li>
                        <li style={{ padding: "6px 0" }}>
                          <a href="/dashboard">Activity</a>
                        </li>
                      </ul>
                      <hr style={{ margin: "8px 0" }} />
                      <button
                        onClick={handleLogout}
                        style={{
                          width: "100%",
                          padding: 8,
                          background: "#e74c3c",
                          color: "#fff",
                          border: "none",
                          borderRadius: 4,
                          cursor: "pointer",
                        }}
                      >
                        Logout
                      </button>
                    </div>
                  )}
                </div> */}
                            </div>
                        </div>
                    </div>

                    {/* --- Mobile Menu --- */}
                    <div className="it_nw_mobile_menu relative-position">
                        {/* Menu Toggle Button */}
                        <div
                            className="it_nw_mobile_menu_button it_nw_open_it_nw_mobile_menu"
                            onClick={toggleMobileMenu}
                        >
                            <i
                                className={`fas ${
                                    menuOpen ? "fa-times-circle" : "fa-bars"
                                }`}
                            ></i>
                        </div>

                        {/* Mobile Menu Wrapper */}
                        <div
                            className={`it_nw_it_nw_mobile_menu_wrap ${
                                menuOpen ? "it_nw_it_nw_mobile_menu_on" : ""
                            }`}
                        >
                            <div className="it_nw_it_nw_mobile_menu_content">
                                <div
                                    className="it_nw_it_nw_mobile_menu_close"
                                    onClick={toggleMobileMenu}
                                >
                                    <i className="far fa-times-circle"></i>
                                </div>

                                <div className="m-brand-logo text-center">
                                    <img
                                        src="/design/assets/new/enteK.svg"
                                        alt="Logo"
                                    />
                                </div>

                                <nav className="main-navigation it_nw_it_nw_mobile_menu-dropdown clearfix ul-li">
                                    <ul
                                        id="main-nav"
                                        className="navbar-nav text-capitalize clearfix"
                                    >
                                        {menus && menus.length > 0 ? (
                                            menus.map((m) => {
                                                const isSubmenuOpen =
                                                    !!openSubmenus[m.id];
                                                return (
                                                    <li
                                                        key={m.id}
                                                        className={
                                                            m?.submenus?.length
                                                                ? "dropdown"
                                                                : undefined
                                                        }
                                                    >
                                                        <a
                                                            href={
                                                                m.slug
                                                                    ? `/${m.slug}`
                                                                    : "/"
                                                            }
                                                            onClick={(e) =>
                                                                handleMenuClick(
                                                                    e,
                                                                    m
                                                                )
                                                            }
                                                        >
                                                            {language === "ml"
                                                                ? m.maltitle ||
                                                                  m.entitle
                                                                : m.entitle}
                                                        </a>
                                                        {m?.submenus?.length ? (
                                                            <>
                                                                <button
                                                                    type="button"
                                                                    className="mobile-submenu-toggle"
                                                                    onClick={() =>
                                                                        toggleSubmenu(
                                                                            m.id
                                                                        )
                                                                    }
                                                                    aria-label={
                                                                        isSubmenuOpen
                                                                            ? "Hide submenu"
                                                                            : "Show submenu"
                                                                    }
                                                                >
                                                                    {isSubmenuOpen
                                                                        ? "-"
                                                                        : "+"}
                                                                </button>
                                                                {isSubmenuOpen && (
                                                                    <ul className="dropdown-menu clearfix submenu-open">
                                                                        {m.submenus
                                                                            .sort(
                                                                                (
                                                                                    a,
                                                                                    b
                                                                                ) =>
                                                                                    (a.order ||
                                                                                        0) -
                                                                                    (b.order ||
                                                                                        0)
                                                                            )
                                                                            .map(
                                                                                (
                                                                                    s
                                                                                ) => (
                                                                                    <li
                                                                                        key={
                                                                                            s.id
                                                                                        }
                                                                                    >
                                                                                        <a
                                                                                            href={`/${s.slug}`}
                                                                                        >
                                                                                            {language ===
                                                                                            "ml"
                                                                                                ? s.maltitle ||
                                                                                                  s.entitle
                                                                                                : s.entitle}
                                                                                        </a>
                                                                                    </li>
                                                                                )
                                                                            )}
                                                                    </ul>
                                                                )}
                                                            </>
                                                        ) : null}
                                                    </li>
                                                );
                                            })
                                        ) : (
                                            <>
                                                <li>
                                                    <a href="/">Home</a>
                                                </li>
                                                {/* {user && (
                      <li>
                        <a href="/dashboard">Activity</a>
                      </li>
                    )} */}
                                                <li>
                                                    <a href="/insight">
                                                        Insights
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="/faq">FAQ</a>
                                                </li>
                                            </>
                                        )}
                                    </ul>
                                </nav>
                                <div className="it_nw_mobile_search_wrapper">
                                    <form
                                        className="it_nw_mobile_search_form"
                                        onSubmit={(e) => e.preventDefault()}
                                    >
                                        <input
                                            type="text"
                                            placeholder="Search..."
                                            className="it_nw_mobile_search_input"
                                        />
                                        <a
                                            href="/"
                                            className="it_nw_mobile_search_btn"
                                        >
                                            <i className="fas fa-search"></i>
                                        </a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
        </>
    );
}
