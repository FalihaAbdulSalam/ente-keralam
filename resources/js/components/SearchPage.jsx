import { useEffect, useMemo, useState } from "react";
import { Link, useLocation, useNavigate } from "react-router-dom";
import { useLanguage } from "./LanguageContext";

export default function SearchPage() {
    const { language } = useLanguage();
    const location = useLocation();
    const navigate = useNavigate();
    const query = useMemo(
        () => new URLSearchParams(location.search),
        [location.search]
    );
    const initialKeyword = query.get("keyword") || "";
    const [keyword, setKeyword] = useState(initialKeyword);
    const [results, setResults] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    // Helper to get display text based on language
    const getDisplayText = (enText, malText) => {
        return language === 'ml' ? (malText || enText) : enText;
    };

    useEffect(() => {
        const currentKeyword = query.get("keyword") || "";
        setKeyword(currentKeyword);
        if (!currentKeyword.trim()) {
            setResults([]);
            return;
        }

        const fetchResults = async () => {
            setLoading(true);
            setError(null);
            try {
                const res = await fetch(
                    `/api/searchkeyword1/?keyword=${encodeURIComponent(
                        currentKeyword.trim()
                    )}`
                );
                const data = await res.json();
                setResults(data?.results?.Article || []);
            } catch (err) {
                setError("Something went wrong. Please try again.");
                setResults([]);
            } finally {
                setLoading(false);
            }
        };

        fetchResults();
    }, [location.search, query]);

    const onSubmit = (e) => {
        e.preventDefault();
        const next = keyword.trim();
        if (!next) return;
        navigate(`/search?keyword=${encodeURIComponent(next)}`);
    };

    return (
        <section className="search-page container py-5">
            <div className="row">
                <div className="col-12">
                    <h2 className="mb-4">Search</h2>
                    <form
                        className="d-flex gap-2 mb-4"
                        onSubmit={onSubmit}
                        role="search"
                    >
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Search..."
                            value={keyword}
                            onChange={(e) => setKeyword(e.target.value)}
                        />
                        <button className="btn btn-primary" type="submit">
                            Go
                        </button>
                    </form>
                </div>
            </div>

            <div className="row">
                <div className="col-12">
                    {loading && <p>Loading results...</p>}
                    {error && <p className="text-danger">{error}</p>}
                    {!loading && !error && results.length === 0 && keyword && (
                        <p>No results found.</p>
                    )}
                    {!loading && !error && results.length > 0 && (
                        <div className="list-group">
                            {results.map((item) => (
                                <Link
                                    key={item.id}
                                    to={`/dept-detail?id=${item.id}`}
                                    className="list-group-item list-group-item-action"
                                >
                                    <div className="d-flex w-100 justify-content-between">
                                        <h5 className="mb-1">
                                            {getDisplayText(item.entitle || "Untitled", item.maltitle)}
                                        </h5>
                                        {item.created_at && (
                                            <small>
                                                {new Date(
                                                    item.created_at
                                                ).toLocaleDateString()}
                                            </small>
                                        )}
                                    </div>
                                    {item.section && (
                                        <small className="text-muted">
                                            {item.section}
                                        </small>
                                    )}
                                </Link>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
}

