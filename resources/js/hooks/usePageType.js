import { useLocation } from 'react-router-dom';

/**
 * Hook to determine the current page type based on the URL
 * Returns one of: 'poll', 'quiz', 'competition', 'pledge', 'task', 'discussion', or null
 */
export const usePageType = () => {
  const location = useLocation();
  const path = location.pathname.toLowerCase();

  // Check for specific activity types in the URL (order matters - most specific first)
  
  // Quiz-related pages
  if (path.includes('/quiz')) {
    return 'quiz';
  }
  
  // Poll/Survey-related pages
  if (path.includes('/poll') || path.includes('/survey')) {
    return 'poll';
  }
  
  // Competition-related pages
  if (path.includes('/competition')) {
    return 'competition';
  }
  
  // Pledge-related pages
  if (path.includes('/pledge')) {
    return 'pledge';
  }
  
  // Task-related pages
  if (path.includes('/task')) {
    return 'task';
  }
  
  // Discussion-related pages
  if (path.includes('/discussion') || path.includes('/discuss')) {
    return 'discussion';
  }

  // Return null for home page and other pages (will show random icon)
  return null;
};
