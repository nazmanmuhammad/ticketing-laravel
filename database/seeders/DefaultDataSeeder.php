<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\System;
use App\Models\SlaSetting;
use App\Models\CannedResponse;
use App\Models\Setting;
use App\Models\Team;
use App\Models\Department;
use App\Models\EmailTemplate;

class DefaultDataSeeder extends Seeder
{
    public function run(): void
    {
        // Categories
        $hardware = Category::firstOrCreate(['name' => 'Hardware']);
        Category::firstOrCreate(['name' => 'Laptop', 'parent_id' => $hardware->id]);
        Category::firstOrCreate(['name' => 'Desktop', 'parent_id' => $hardware->id]);
        Category::firstOrCreate(['name' => 'Printer', 'parent_id' => $hardware->id]);

        $software = Category::firstOrCreate(['name' => 'Software']);
        Category::firstOrCreate(['name' => 'OS Issue', 'parent_id' => $software->id]);
        Category::firstOrCreate(['name' => 'Application', 'parent_id' => $software->id]);
        Category::firstOrCreate(['name' => 'License', 'parent_id' => $software->id]);

        $network = Category::firstOrCreate(['name' => 'Network']);
        Category::firstOrCreate(['name' => 'VPN', 'parent_id' => $network->id]);
        Category::firstOrCreate(['name' => 'Wi-Fi', 'parent_id' => $network->id]);
        Category::firstOrCreate(['name' => 'Internet', 'parent_id' => $network->id]);

        Category::firstOrCreate(['name' => 'Email & Communication']);
        Category::firstOrCreate(['name' => 'Account & Access']);
        Category::firstOrCreate(['name' => 'Other']);

        // Systems
        System::firstOrCreate(['name' => 'ERP System', 'description' => 'Enterprise Resource Planning']);
        System::firstOrCreate(['name' => 'CRM System', 'description' => 'Customer Relationship Management']);
        System::firstOrCreate(['name' => 'HRIS', 'description' => 'Human Resource Information System']);
        System::firstOrCreate(['name' => 'Email Server', 'description' => 'Corporate Email System']);
        System::firstOrCreate(['name' => 'File Server', 'description' => 'Shared File Storage']);
        System::firstOrCreate(['name' => 'VPN Gateway', 'description' => 'Remote Access VPN']);
        System::firstOrCreate(['name' => 'Active Directory', 'description' => 'User Directory Service']);

        // SLA Settings
        SlaSetting::firstOrCreate(['priority' => 'low'], ['response_hours' => 24, 'resolution_hours' => 72]);
        SlaSetting::firstOrCreate(['priority' => 'medium'], ['response_hours' => 8, 'resolution_hours' => 24]);
        SlaSetting::firstOrCreate(['priority' => 'high'], ['response_hours' => 2, 'resolution_hours' => 8]);
        SlaSetting::firstOrCreate(['priority' => 'critical'], ['response_hours' => 1, 'resolution_hours' => 2]);

        // Canned Responses
        CannedResponse::firstOrCreate(['title' => 'Acknowledge Receipt'], ['body' => 'Thank you for contacting the helpdesk. We have received your request and will get back to you shortly.']);
        CannedResponse::firstOrCreate(['title' => 'Need More Info'], ['body' => 'Could you please provide more details about the issue? This will help us assist you more effectively.']);
        CannedResponse::firstOrCreate(['title' => 'Issue Resolved'], ['body' => 'The reported issue has been resolved. Please verify and let us know if you need further assistance.']);
        CannedResponse::firstOrCreate(['title' => 'Escalated'], ['body' => 'Your ticket has been escalated to a senior technician for further investigation.']);

        // Teams
        Team::firstOrCreate(['name' => 'IT Support', 'description' => 'Level 1 IT Support']);
        Team::firstOrCreate(['name' => 'Network Team', 'description' => 'Network Infrastructure']);
        Team::firstOrCreate(['name' => 'DevOps', 'description' => 'Development Operations']);
        Team::firstOrCreate(['name' => 'Security', 'description' => 'Information Security']);

        // Departments
        Department::firstOrCreate(['name' => 'IT', 'description' => 'Information Technology']);
        Department::firstOrCreate(['name' => 'HR', 'description' => 'Human Resources']);
        Department::firstOrCreate(['name' => 'Finance', 'description' => 'Finance & Accounting']);
        Department::firstOrCreate(['name' => 'Operations', 'description' => 'Business Operations']);
        Department::firstOrCreate(['name' => 'Marketing', 'description' => 'Marketing & Sales']);

        // Settings
        Setting::setValue('auto_close_days', '7');
        Setting::setValue('app_name', 'Helpdesk');
        Setting::setValue('timezone', 'UTC');

        // Email Templates
        $templates = [
            ['event' => 'ticket_created', 'subject' => 'New Ticket Created: {ticket_number}', 'body' => 'A new ticket has been created.\n\nTicket: {ticket_number}\nTitle: {title}\nPriority: {priority}\n\nPlease login to view details.'],
            ['event' => 'ticket_assigned', 'subject' => 'Ticket Assigned: {ticket_number}', 'body' => 'Ticket {ticket_number} has been assigned to you.\n\nTitle: {title}\nPriority: {priority}\n\nPlease login to view and respond.'],
            ['event' => 'ticket_replied', 'subject' => 'New Reply on Ticket: {ticket_number}', 'body' => 'There is a new reply on ticket {ticket_number}.\n\nPlease login to view the response.'],
            ['event' => 'ticket_resolved', 'subject' => 'Ticket Resolved: {ticket_number}', 'body' => 'Ticket {ticket_number} has been marked as resolved.\n\nIf the issue persists, please reopen the ticket.'],
            ['event' => 'access_request_submitted', 'subject' => 'Access Request Submitted: {request_number}', 'body' => 'A new access request {request_number} requires your approval.\n\nSystem: {system}\nRequester: {requester}'],
            ['event' => 'access_request_approved', 'subject' => 'Access Request Approved: {request_number}', 'body' => 'Your access request {request_number} has been approved.'],
            ['event' => 'access_request_rejected', 'subject' => 'Access Request Rejected: {request_number}', 'body' => 'Your access request {request_number} has been rejected.\n\nReason: {notes}'],
            ['event' => 'change_request_submitted', 'subject' => 'Change Request Submitted: {request_number}', 'body' => 'A new change request {request_number} requires review.\n\nTitle: {title}\nType: {change_type}'],
            ['event' => 'change_request_approved', 'subject' => 'Change Request Approved: {request_number}', 'body' => 'Change request {request_number} has been approved and is ready for scheduling.'],
        ];
        foreach ($templates as $t) {
            EmailTemplate::firstOrCreate(['event' => $t['event']], $t);
        }
    }
}
