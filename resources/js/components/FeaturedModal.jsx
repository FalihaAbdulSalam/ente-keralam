import { useState, useEffect } from 'react';

function FeaturedModal() {
  const [isOpen, setIsOpen] = useState(false);

  useEffect(() => {
    // Only check localStorage for permanent dismissal
    const permanentlyDismissed = localStorage.getItem('featuredModalDismissed');
    if (!permanentlyDismissed) {
      setIsOpen(true);
    }
  }, []);

  // Temporary close - just closes modal, will show again on next visit
  const handleClose = () => {
    setIsOpen(false);
  };

  // Permanent close - won't show again
  const handleDontShowAgain = () => {
    setIsOpen(false);
    localStorage.setItem('featuredModalDismissed', 'true');
  };

  const handleOverlayClick = (e) => {
    if (e.target === e.currentTarget) {
      handleClose();
    }
  };

  if (!isOpen) return null;

  return (
    <div 
      className="featured-modal-overlay"
      onClick={handleOverlayClick}
    >
      <div className="featured-modal">
        <button 
          className="featured-modal-close"
          onClick={handleClose}
          aria-label="Close modal"
        >
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
        
        <div className="featured-modal-content">
          <div className="featured-modal-badge">Featured</div>
          
          <img 
            src="/uploads/quizzes/posters/cm_mega_quiz.jpeg" 
            alt="Chief Minister's Mega Quiz"
            className="featured-modal-image"
          />
          
          <div className="featured-modal-actions">
            <a 
              href="/cmmegaquiz" 
              className="featured-modal-btn"
              onClick={handleClose}
            >
              Learn More
            </a>
            <button 
              className="featured-modal-dismiss"
              onClick={handleDontShowAgain}
            >
              Don't show again
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}

export default FeaturedModal;
