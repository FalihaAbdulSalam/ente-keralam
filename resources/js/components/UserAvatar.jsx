
/**
 * Reusable User Avatar Component
 * Shows user's profile picture if available, otherwise shows initials with colored background
 */
export default function UserAvatar({ user, size = 120, fontSize = 48, className = '' }) {
    // Generate consistent color based on user name
    const getAvatarColor = (name) => {
        const colors = [
            '#FF6B6B', '#4ECDC4', '#45B7D1', '#FFA07A', '#98D8C8',
            '#F7DC6F', '#BB8FCE', '#85C1E2', '#F8B739', '#52B788',
            '#E76F51', '#2A9D8F', '#E9C46A', '#F4A261', '#264653',
            '#8338EC', '#3A86FF', '#FB5607', '#FF006E', '#FFBE0B'
        ];
        const index = name ? name.charCodeAt(0) % colors.length : 0;
        return colors[index];
    };

    // Get first letter of user name
    const getInitial = (name) => {
        return name ? name.charAt(0).toUpperCase() : 'U';
    };

    const userName = user?.name || 'User';
    const avatarUrl = user?.avatar_url || user?.avatar;

    const avatarStyle = {
        width: `${size}px`,
        height: `${size}px`,
        borderRadius: '50%',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        fontSize: `${fontSize}px`,
        fontWeight: '600',
        color: 'white',
        textTransform: 'uppercase',
        objectFit: 'cover',
    };

    if (avatarUrl) {
        return (
            <img 
                src={avatarUrl} 
                alt={userName} 
                className={className}
                style={avatarStyle}
            />
        );
    }

    return (
        <div 
            className={className}
            style={{
                ...avatarStyle,
                backgroundColor: getAvatarColor(userName)
            }}
        >
            {getInitial(userName)}
        </div>
    );
}
