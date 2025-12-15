# Design Document: Technician Dashboard Auto-Refresh

## Overview

This feature implements automatic page refresh functionality for technician dashboard pages using JavaScript's `setInterval` function. The implementation will be simple, reliable, and ensure technicians always see the most current booking information.

## Architecture

The solution uses a client-side JavaScript approach with the following components:

1. **Auto-Refresh Script**: JavaScript code added to target pages that sets up a 10-second interval timer
2. **URL Preservation**: Maintains current URL parameters (filters, search queries) during refresh
3. **Page-Specific Implementation**: Applied selectively to dashboard pages where real-time updates are beneficial

### Design Decision: Full Page Reload vs. AJAX Updates

**Chosen Approach**: Full page reload using `window.location.reload()`

**Rationale**:
- Simpler implementation with no risk of memory leaks
- Ensures all page elements are updated (not just specific sections)
- Avoids complex state management
- Existing page already has some AJAX polling; full reload provides a complementary approach
- 10-second interval is reasonable for full page reloads given modern connection speeds

## Components and Interfaces

### 1. Auto-Refresh Script Module

**Location**: Inline `<script>` tag at the end of each target page's HTML

**Interface**:
```javascript
// Simple setInterval that reloads the page every 10 seconds
setInterval(function() {
    window.location.reload();
}, 10000); // 10000ms = 10 seconds
```

**Target Pages**:
- `tech/dashboard.php` - Main dashboard with booking list
- `tech/notifications.php` - Notifications page
- `tech/completed-bookings.php` - Completed bookings view

**Excluded Pages**:
- `tech/my-profile.php` - Form-based page where refresh would disrupt user input
- `tech/booking-details.php` - Already has AJAX status checking
- `tech/complete-booking.php` - Action page with redirects
- `tech/change-password.php` - Form-based page
- `tech/collect-payment.php` - Payment collection page where refresh would disrupt transaction
- `tech/complete-service.php` - Service completion page where refresh would disrupt form submission

### 2. URL Parameter Preservation

**Mechanism**: Built-in browser behavior

When using `window.location.reload()`, the browser automatically:
- Preserves all URL parameters (filter, search)
- Maintains the current URL state
- Resubmits GET requests with same parameters

**Example**:
- Current URL: `dashboard.php?filter=pending&search=12345`
- After reload: Same URL with same parameters
- Result: Filter and search state preserved

## Data Models

No new data models required. The feature operates entirely on the client side using existing page URLs and parameters.

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system—essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Refresh Interval Consistency

*For any* technician dashboard page with auto-refresh enabled, the page should reload at consistent 10-second intervals from the time the page initially loads.

**Validates: Requirements 1.1, 3.1**

### Property 2: URL State Preservation

*For any* dashboard page with URL parameters (filter, search), reloading the page should result in the same URL parameters being present after the reload.

**Validates: Requirements 1.2, 1.3**

### Property 3: Independent Tab Behavior

*For any* number of open technician dashboard tabs, each tab should refresh independently on its own 10-second cycle without affecting other tabs.

**Validates: Requirements 3.4**

## Error Handling

### Network Failures

**Scenario**: Page reload fails due to network connectivity issues

**Handling**: 
- Browser's built-in error handling displays standard error page
- User can manually refresh when connection is restored
- Timer continues in background (though page may not be functional)

**Mitigation**: None required - standard browser behavior is acceptable

### Server Errors

**Scenario**: Server returns 500 error or is unavailable

**Handling**:
- Browser displays error page
- User sees error and can take appropriate action
- No special handling needed

## Testing Strategy

### Unit Testing

Given the simplicity of this feature (a single `setInterval` call), traditional unit tests are not applicable. The implementation is a direct browser API call with no custom logic to test.

### Manual Testing

**Test Case 1: Basic Auto-Refresh**
1. Open `tech/dashboard.php`
2. Note the current time
3. Wait and observe page reload after 10 seconds
4. Verify reload occurs consistently every 10 seconds

**Test Case 2: Filter Preservation**
1. Open `tech/dashboard.php`
2. Click "Pending" filter
3. Wait for auto-refresh
4. Verify "Pending" filter remains active after reload

**Test Case 3: Search Preservation**
1. Open `tech/dashboard.php`
2. Enter search term
3. Wait for auto-refresh
4. Verify search term remains in search box after reload

**Test Case 4: Multiple Tabs**
1. Open `tech/dashboard.php` in two browser tabs
2. Observe that tabs refresh independently
3. Verify each tab maintains its own 10-second cycle

**Test Case 5: Page Exclusions**
1. Open `tech/my-profile.php`
2. Wait 15 seconds
3. Verify page does NOT auto-refresh

### Browser Compatibility

**Target Browsers**: All modern browsers (Chrome, Firefox, Safari, Edge)

**Compatibility**: `setInterval` and `window.location.reload()` are supported in all browsers, including IE11

## Implementation Notes

### Placement of Script

The auto-refresh script should be placed at the end of the page, just before the closing `</body>` tag, to ensure:
- Page is fully loaded before timer starts
- No interference with other page initialization scripts
- Clean separation from other JavaScript code

### Interaction with Existing AJAX Polling

The dashboard page currently has AJAX polling that checks for booking changes every 10 seconds. The new full page reload will:
- Complement the existing AJAX by ensuring all page elements are fresh
- Reset the AJAX polling timer on each reload (new timer starts)
- Provide redundancy in case AJAX polling fails

### Performance Considerations

**Network Impact**: 
- Full page reload every 10 seconds
- Typical dashboard page size: ~100-200KB
- Data usage: ~0.6-1.2MB per minute
- Acceptable for dashboard monitoring use case

**Server Impact**:
- Additional page requests: 6 per minute per active technician
- Minimal server load given typical technician count
- All requests are standard GET requests with caching headers

## Alternative Approaches Considered

### 1. AJAX Partial Updates
**Pros**: Lower bandwidth, smoother UX
**Cons**: Complex implementation, potential for stale UI elements, memory leaks
**Decision**: Rejected in favor of simplicity

### 2. WebSocket Real-Time Updates
**Pros**: True real-time updates, efficient
**Cons**: Requires server-side WebSocket infrastructure, complex implementation
**Decision**: Rejected as over-engineering for this use case

### 3. Server-Sent Events (SSE)
**Pros**: Simpler than WebSocket, real-time
**Cons**: Still requires server-side changes, browser compatibility concerns
**Decision**: Rejected in favor of simpler solution

## Future Enhancements

1. **Configurable Interval**: Allow admin to configure refresh interval
2. **Pause on Interaction**: Detect user interaction and pause refresh temporarily
3. **Smart Refresh**: Only refresh if data has changed (requires server-side support)
4. **Visual Countdown**: Show countdown timer to next refresh
5. **Offline Detection**: Pause refresh when offline, resume when online
