-- Step 1: Find available rentals that might need invoices
-- This will show you all rentals and their details so you can identify which one invoice ID 1 should belong to

SELECT 
    r.rental_id,
    r.status as rental_status,
    rr.rental_request_id,
    t.tenant_id,
    t.name as tenant_name,
    t.email as tenant_email,
    p.property_name,
    u.unit_number,
    r.created_at as rental_created
FROM rentals r
JOIN rental_requests rr ON r.rental_request_id = rr.rental_request_id
JOIN tenants t ON rr.tenant_id = t.tenant_id
JOIN properties p ON rr.property_id = p.property_id
JOIN units u ON rr.unit_id = u.unit_id
WHERE r.status = 'active'
ORDER BY r.created_at DESC;

-- Step 2: Check current invoice data
SELECT 
    invoice_id,
    rental_id,
    booking_id,
    amount,
    status,
    issue_date,
    due_date
FROM invoices 
WHERE invoice_id = 1;

-- Step 3: After you identify the correct rental_id from Step 1, 
-- replace [CORRECT_RENTAL_ID] with the actual rental_id and run this:
/*
UPDATE invoices 
SET rental_id = [CORRECT_RENTAL_ID], booking_id = NULL 
WHERE invoice_id = 1;
*/

-- Step 4: Verify the update worked
/*
SELECT 
    i.invoice_id,
    i.rental_id,
    i.booking_id,
    t.name as tenant_name,
    t.email as tenant_email
FROM invoices i
LEFT JOIN rentals r ON i.rental_id = r.rental_id
LEFT JOIN rental_requests rr ON r.rental_request_id = rr.rental_request_id
LEFT JOIN tenants t ON rr.tenant_id = t.tenant_id
WHERE i.invoice_id = 1;
*/
