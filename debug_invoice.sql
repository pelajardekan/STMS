-- Debug Invoice Data
-- Run this query to check the invoice and its relationships

SELECT 
    i.invoice_id,
    i.rental_id,
    i.booking_id,
    i.amount,
    i.status,
    i.created_at,
    
    -- Rental Info
    r.rental_id as rental_exists,
    rr.rental_request_id as rental_request_exists,
    rt.tenant_id as rental_tenant_id,
    rt.name as rental_tenant_name,
    
    -- Booking Info  
    b.booking_id as booking_exists,
    br.booking_request_id as booking_request_exists,
    bt.tenant_id as booking_tenant_id,
    bt.name as booking_tenant_name

FROM invoices i
LEFT JOIN rentals r ON i.rental_id = r.rental_id
LEFT JOIN rental_requests rr ON r.rental_request_id = rr.rental_request_id  
LEFT JOIN tenants rt ON rr.tenant_id = rt.tenant_id

LEFT JOIN bookings b ON i.booking_id = b.booking_id
LEFT JOIN booking_requests br ON b.booking_request_id = br.booking_request_id
LEFT JOIN tenants bt ON br.tenant_id = bt.tenant_id

WHERE i.invoice_id = 1;

-- Also check all invoices to see the pattern
SELECT 
    invoice_id,
    rental_id,
    booking_id,
    status,
    amount
FROM invoices 
ORDER BY invoice_id;
