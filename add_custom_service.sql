-- Add Custom Service entry to tms_service table
-- This allows customers to book services not in the standard catalog

INSERT INTO tms_service (s_name, s_category, s_price, s_duration, s_description, s_status) 
VALUES (
    'Custom Service Request', 
    'Custom Service', 
    0, 
    'To be determined', 
    'Customer requested custom service - price and duration to be quoted by admin', 
    'Active'
)
ON DUPLICATE KEY UPDATE 
    s_status = 'Active';

-- Note: This service will be automatically created when first custom booking is made
-- But you can run this SQL to create it in advance
