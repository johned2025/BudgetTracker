// neonCursor.js
import { neonCursor } from 'https://unpkg.com/threejs-toys@0.0.8/build/threejs-toys.module.cdn.min.js';

// Initialize the neonCursor effect
neonCursor({
    el: document.body,                     // Target element for the effect
    shaderPoints: 16,                      // Number of shader points for the cursor
    curvePoints: 80,                       // Number of points for the curve of the trail
    curveLerp: 0.5,                        // Smoothness of the cursor movement
    radius1: 5,                            // Inner radius of the cursor effect
    radius2: 30,                           // Outer radius of the cursor effect
    velocityTreshold: 10,                  // Speed threshold for triggering the effect
    sleepRadiusX: 500,                     // Horizontal area in which the cursor can "sleep"
    sleepRadiusY: 500,                     // Vertical area in which the cursor can "sleep"
    sleepTimeCoefX: 0.0025,                // Horizontal sleep time coefficient
    sleepTimeCoefY: 0.0025                 // Vertical sleep time coefficient
});


