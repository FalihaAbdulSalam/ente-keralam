import { useState, useEffect, createContext, useContext } from 'react';
import { Carousel } from 'react-bootstrap';
import { bannersAPI } from '../services/api';
import { useLanguage } from './LanguageContext';
import './Carousel.css';

// Create context for carousel loading state
const CarouselLoadingContext = createContext();

export const useCarouselLoading = () => {
  const context = useContext(CarouselLoadingContext);
  return context || { imagesLoaded: true }; // Default to loaded if not in context
};

// Default images fallback
const DEFAULT_CAROUSEL_IMAGES = [
  { src: '/design/assets/banner/banner-1.1.jpg', alt: 'First slide' },
  { src: '/design/assets/banner/banner-1.jpg', alt: 'Second slide' },
  { src: '/design/assets/banner/banners-04.jpg', alt: 'Third slide' },
];

export function CarouselLoadingProvider({ children }) {
  const { language } = useLanguage();
  const [imagesLoaded, setImagesLoaded] = useState(false);
  const [carouselImages, setCarouselImages] = useState([]);

  // Helper to get display text based on language
  const getDisplayText = (enText, malText) => {
    return language === 'ml' ? (malText || enText) : enText;
  };

  // Fetch banner data from API
  useEffect(() => {
    const fetchBanners = async () => {
      try {
        const response = await bannersAPI.getAll();
        if (response.data.status && response.data.data && response.data.data.length > 0) {
          const images = response.data.data.map((banner) => ({
            src: banner.poster,
            alt: getDisplayText(banner.entitle || 'Banner', banner.maltitle),
          }));
          setCarouselImages(images);
        } else {
          setCarouselImages(DEFAULT_CAROUSEL_IMAGES);
        }
      } catch (error) {
        console.error('Error fetching banners:', error);
        setCarouselImages(DEFAULT_CAROUSEL_IMAGES);
      }
    };

    fetchBanners();
  }, []);

  // Preload all carousel images to prevent layout shift
  useEffect(() => {
    if (carouselImages.length === 0) {
      setImagesLoaded(true);
      return;
    }

    let loadedCount = 0;
    const totalImages = carouselImages.length;

    carouselImages.forEach((image) => {
      const link = document.createElement('link');
      link.rel = 'preload';
      link.as = 'image';
      link.href = image.src;
      document.head.appendChild(link);

      // Track image loading
      const img = new Image();
      img.onload = () => {
        loadedCount++;
        if (loadedCount === totalImages) {
          setImagesLoaded(true);
        }
      };
      img.onerror = () => {
        loadedCount++;
        if (loadedCount === totalImages) {
          setImagesLoaded(true);
        }
      };
      img.src = image.src;
    });
  }, [carouselImages]);

  return (
    <CarouselLoadingContext.Provider value={{ imagesLoaded, carouselImages }}>
      {children}
    </CarouselLoadingContext.Provider>
  );
}

export default function HeroCarousel() {
  const [index, setIndex] = useState(0);
  const { imagesLoaded, carouselImages = [] } = useCarouselLoading();

  const handleSelect = (selectedIndex) => {
    setIndex(selectedIndex);
  };

  // Use API data or fallback to default
  const images = carouselImages.length > 0 ? carouselImages : DEFAULT_CAROUSEL_IMAGES;

  return (
    <div className={`position-relative ek-carousel ${!imagesLoaded ? 'loading' : ''}`}>
      {/* Custom OL LI Indicators */}
      <ol className="carousel-indicators">
        {images.map((_, i) => (
          <li
            key={i}
            data-slide-to={i}
            className={i === index ? 'active' : ''}
            onClick={() => handleSelect(i)}
            style={{ cursor: 'pointer' }}
          ></li>
        ))}
      </ol>

      {/* Carousel */}
      <Carousel activeIndex={index} onSelect={handleSelect} interval={2000} controls={false} indicators={false}>
        {images.map((image, i) => (
          <Carousel.Item key={i}>
            <img 
              className="d-block w-100 carousel-image" 
              src={image.src} 
              alt={image.alt}
              loading="eager"
              fetchPriority={i === 0 ? "high" : "low"}
            />
          </Carousel.Item>
        ))}
      </Carousel>
    </div>
  );
}
