import { createContext, useContext, useState, useEffect } from "react";

const LanguageContext = createContext();

export const useLanguage = () => {
  const context = useContext(LanguageContext);
  if (!context) {
    throw new Error('useLanguage must be used within a LanguageProvider');
  }
  return context;
};

export function LanguageProvider({ children }) {
  const [language, setLanguage] = useState(() => {
    // Default to Malayalam if nothing is stored
    return localStorage.getItem('language') || 'ml';
  });

  useEffect(() => {
    localStorage.setItem('language', language);
  }, [language]);

  const toggleLanguage = (lang) => {
    setLanguage(lang);
  };

  const value = {
    language,
    toggleLanguage,
    isEnglish: language === 'en',
    isMalayalam: language === 'ml',
  };

  return (
    <LanguageContext.Provider value={value}>
      {children}
    </LanguageContext.Provider>
  );
}
