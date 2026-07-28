/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        "./src/Resources/**/*.blade.php",
        "./src/Resources/**/*.js",
        "../../../resources/themes/**/*.blade.php",
        "../../../resources/themes/**/*.js",
    ],

    theme: {
        container: {
            center: true,

            screens: {
                "2xl": "1600px",
            },

            padding: {
                DEFAULT: "24px",
                md: "40px",
                lg: "56px",
            },
        },

        screens: {
            sm: "525px",
            md: "768px",
            lg: "1024px",
            xl: "1240px",
            "2xl": "1440px",
            1180: "1180px",
            1060: "1060px",
            991: "991px",
            868: "868px",
        },

        extend: {
            colors: {
                // Neutral grayscale palette (token names preserved from previous warm palette)
                cream:      "#FAFAFA",
                canvas:     "#F4F4F4",
                sand:       "#E5E5E5",
                tan:        "#D4D4D4",
                mist:       "#D4D4D4",
                clay:       "#A3A3A3",
                stone:      "#737373",
                cocoa:      "#404040",
                ink:        "#171717",
                rust:       "#525252",
                forest:     "#525252",

                // Legacy aliases
                navyBlue:   "#171717",
                lightOrange:"#FAFAFA",
                darkGreen:  "#525252",
                darkBlue:   "#404040",
                darkPink:   "#525252",
            },

            fontFamily: {
                poppins:    ["\"Plus Jakarta Sans\"", "system-ui", "-apple-system", "sans-serif"],
                sans:       ["\"Plus Jakarta Sans\"", "system-ui", "-apple-system", "sans-serif"],
                dmserif:    ["\"Plus Jakarta Sans\"", "system-ui", "-apple-system", "sans-serif"],
                serif:      ["\"Plus Jakarta Sans\"", "system-ui", "-apple-system", "sans-serif"],
            },

            fontSize: {
                'display':  ['clamp(2.5rem, 6vw, 5rem)', { lineHeight: '1.05', letterSpacing: '-0.02em' }],
                'hero':     ['clamp(2rem, 4vw, 3.5rem)', { lineHeight: '1.1',  letterSpacing: '-0.015em' }],
                'section':  ['clamp(1.5rem, 2.5vw, 2.25rem)', { lineHeight: '1.15' }],
            },

            borderRadius: {
                'brand': '2px',
                'card':  '4px',
                'pill':  '999px',
            },

            letterSpacing: {
                'widelg': '0.14em',
            },

            boxShadow: {
                'soft':  '0 2px 12px rgba(26, 22, 19, 0.06)',
                'lift':  '0 8px 24px rgba(26, 22, 19, 0.08)',
            },

            transitionDuration: {
                '400': '400ms',
            },
        }
    },

    plugins: [],

    safelist: [
        {
            pattern: /icon-/,
        }
    ]
};
