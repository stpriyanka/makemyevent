# Make My Event - AI Coding Agent Instructions

## Project Overview
This is a premium wedding event planning website for "Make My Event" - a Bangladeshi event management company established in 2015. The site showcases a luxury wedding portfolio with immersive visual experiences and sophisticated interactions.

## Architecture & Tech Stack
- **Pure HTML/CSS/JS** - No build process or frameworks
- **Single-page application** with smooth scrolling navigation
- **CDN dependencies**: LightGallery (2.7.2) for photo galleries
- **Google Fonts**: Playfair Display, Great Vibes, Inter
- **Local assets**: Custom fonts (AcademiaSSK, Tangerine, Themunday, Montserrat) in `assets/fonts/`

## Core Design Patterns

### Visual Layout System
- **Full-viewport sections** with `min-height: 100vh` for immersive experiences  
- **Layered backgrounds**: Fixed positioned `<img>` elements with `object-fit: cover` for responsive backgrounds
- **Overlay technique**: `::after` pseudo-elements with `rgba()` gradients for readability
- **Z-index layering**: Background (0) → Overlay (1) → Content (2+)

### Typography Hierarchy
```css
/* Consistent eyebrow → title → content pattern */
.eyebrow { text-transform: uppercase; letter-spacing: .22em; }
.title { font-family: "Playfair Display", serif; font-size: clamp(); }
.script { font-family: "Great Vibes", cursive; } /* Decorative headers */
```

### Responsive Strategy
- **Fluid typography**: `clamp(min, preferred, max)` throughout
- **Progressive layout**: Grid → Stack → Single-column for mobile
- **Image positioning**: Complex `calc()` expressions for precise multi-image layouts
- **Safe areas**: `env(safe-area-inset-*)` for mobile notches

### Asset Organization
```
assets/
├── fonts/          # Local @font-face files (AcademiaSSK, Tangerine, etc.)
├── images/
    ├── album[1-11]/ # Gallery images (cover-thumb.jpg, img[N]-large/thumb.jpg)
    ├── team/        # Team member PNGs with transparent backgrounds
    └── [backgrounds, frames, UI elements]
```

## Development Conventions

### CSS Architecture
- **Embedded styles**: Section-specific `<style>` blocks in HTML for tight coupling
- **Shared styles**: Global styles in `styles.css` (nav, typography, utilities)
- **BEM-like naming**: `.segment-card-inner`, `.about-copy`, `.decor-frames`
- **Utility classes**: `.frame` (bordered cards), `.container` (max-width wrapper)

### Interaction Patterns
- **Modal systems**: CSS-only (`:target` pseudo-class) and JavaScript-driven lightboxes
- **Hover animations**: `transform: translateY() scale()` with smooth transitions
- **Scroll behaviors**: `scroll-margin-top` for sticky nav compensation
- **Focus management**: Manual focus restoration in complex interactions

### Image Handling
- **Dual resolution**: `-thumb.jpg` and `-large.jpg` conventions for performance
- **Gallery structure**: JSON data attributes for album configurations
- **Background techniques**: Mix of CSS `background-image` and positioned `<img>` elements
- **Aspect ratios**: `aspect-ratio` property for consistent proportions

## Key Features & Components

### Gallery System (LightGallery Integration)
```javascript
// Album data stored as HTML data attributes
data-album='[{"src": "large.jpg", "thumb": "thumb.jpg"}]'
// Custom initialization with cleanup handlers for modal state
```

### Multi-Modal Interfaces
- **Segments**: CSS-only modals using `:target` with decorative arch overlays
- **Gallery**: JavaScript lightbox with album browsing
- **Testimonials**: Scrolling carousel with popup previews

### Animation Philosophy  
- **Subtle motion**: 200-400ms transitions, slight lift effects (`translateY(-6px)`)
- **Performance conscious**: `will-change`, `transform-origin`, GPU-accelerated properties
- **Accessibility**: `@media (prefers-reduced-motion)` overrides

## Code Style Guidelines

### HTML Structure
- Semantic sections with descriptive IDs matching navigation
- `aria-hidden="true"` for decorative elements
- Progressive enhancement (works without JavaScript)

### CSS Best Practices  
```css
/* Consistent responsive patterns */
@media (max-width: 980px) { /* tablet */ }
@media (max-width: 640px) { /* mobile */ }

/* Fixed positioning with proper z-index */
.modal { position: fixed; inset: 0; z-index: 1000; }

/* Clamp for fluid scaling */
font-size: clamp(14px, 1.5vw, 18px);
```

### JavaScript Approach
- **Vanilla JS**: No frameworks, minimal dependencies
- **Event delegation**: Efficient handling for dynamic content  
- **Error-safe**: Null checks and graceful degradation
- **Performance**: RequestAnimationFrame for DOM manipulation

## Development Workflow

### Adding New Sections
1. Follow the `.section` → `.container` → `.content` hierarchy
2. Use fullscreen background pattern: positioned `<img>` + overlay
3. Implement responsive breakpoints at 980px and 640px
4. Add smooth scroll navigation link

### Working with Images
- **Optimization**: Use appropriate formats (JPG backgrounds, PNG with transparency)
- **Naming**: Descriptive names, avoid spaces (use %20 encoding if needed)  
- **Albums**: Follow `albumN/cover-thumb.jpg`, `imgN-large/thumb.jpg` pattern

### Testing Considerations
- **Cross-browser**: Safari, Chrome, Firefox (backdrop-filter support)
- **Mobile**: iOS Safari safe areas, Android viewport handling
- **Performance**: Large images, smooth animations, memory cleanup

## Common Gotchas
- **Font loading**: Local fonts may need fallbacks for loading states
- **Z-index stacking**: Fixed backgrounds require careful layering
- **Modal cleanup**: LightGallery instances need proper destruction
- **Scroll restoration**: Complex modals can break browser back behavior

When modifying this codebase, maintain the luxury aesthetic, smooth interactions, and mobile-first responsive approach. Always test hover states on touch devices and ensure accessibility standards are met.