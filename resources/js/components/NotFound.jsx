"use client";
import { Link } from "react-router-dom";
import { FaHome, FaArrowLeft } from "react-icons/fa";

export default function NotFound() {
    return (
        <>
            <div className="wraper1 position-relative">
                {/* Background foot images */}
                <div className="footm" style={{bottom: '0' }}>
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                    <img src="/design/assets/footm.png" alt="" />
                </div>

                {/* 404 Section */}
                <section id="it-up-404" className="it-up-404-section position-relative" style={{ minHeight: '70vh', display: 'flex', alignItems: 'center' }}>
                    <div className="container">
                        <div className="row">
                            <div className="col-lg-8 col-md-10 col-12 m-auto">
                                <div className="text-center" style={{ paddingTop: '40px', paddingBottom: '40px' }}>
                                    {/* Logo */}
                                    <div style={{ marginBottom: '30px' }}>
                                        <img src="/design/assets/new/enteK.svg" alt="Ente Keralam Logo" style={{ maxWidth: '200px', height: 'auto' }} />
                                    </div>

                                    {/* 404 Text */}
                                    <h1 style={{
                                        fontSize: '80px',
                                        fontWeight: '900',
                                        color: '#ff176b',
                                        margin: '20px 0',
                                        textShadow: '2px 2px 4px rgba(0,0,0,0.1)'
                                    }}>
                                        404
                                    </h1>

                                    <h2 style={{
                                        fontSize: '32px',
                                        fontWeight: '700',
                                        color: '#333',
                                        margin: '20px 0'
                                    }}>
                                        Page Not Found
                                    </h2>

                                    <p style={{
                                        fontSize: '16px',
                                        color: '#666',
                                        marginBottom: '30px',
                                        lineHeight: '1.6'
                                    }}>
                                        The page you requested could not be found. Please navigate to the home page or try again.
                                    </p>

                                    {/* Action Buttons */}
                                    <div style={{
                                        display: 'flex',
                                        gap: '15px',
                                        justifyContent: 'center',
                                        flexWrap: 'wrap',
                                        marginTop: '40px'
                                    }}>
                                        <Link 
                                            to="/" 
                                            className="btn btn-lg color-ruby text-uppercase"
                                            style={{
                                                padding: '12px 30px',
                                                borderRadius: '4px',
                                                display: 'inline-flex',
                                                alignItems: 'center',
                                                gap: '10px',
                                                textDecoration: 'none',
                                                fontWeight: '700'
                                            }}>
                                            <FaHome /> Back to Home
                                        </Link>

                                        <button 
                                            onClick={() => window.history.back()}
                                            className="btn btn-lg text-uppercase"
                                            style={{
                                                padding: '12px 30px',
                                                borderRadius: '4px',
                                                border: '2px solid #039',
                                                backgroundColor: 'transparent',
                                                color: '#039',
                                                display: 'inline-flex',
                                                alignItems: 'center',
                                                gap: '10px',
                                                textDecoration: 'none',
                                                fontWeight: '700',
                                                cursor: 'pointer',
                                                transition: 'all 0.3s ease'
                                            }}
                                            onMouseEnter={(e) => {
                                                e.target.style.backgroundColor = '#039';
                                                e.target.style.color = 'white';
                                            }}
                                            onMouseLeave={(e) => {
                                                e.target.style.backgroundColor = 'transparent';
                                                e.target.style.color = '#039';
                                            }}>
                                            <FaArrowLeft /> Go Back
                                        </button>
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
