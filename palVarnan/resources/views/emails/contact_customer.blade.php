<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Contacting Pal Varnan</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f7f6; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; color: #1e293b;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f4f7f6; padding: 40px 16px;">
        <tr>
            <td align="center">
                <!-- Main Container -->
                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 620px; background-color: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid #e2e8f0;">
                    
                    <!-- Header Banner -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #0c716a 0%, #059669 100%); padding: 36px 36px 32px; text-align: center;">
                            <h1 style="margin: 0; font-size: 26px; font-weight: 800; color: #ffffff; letter-spacing: -0.5px;">
                                Pal Varnan
                            </h1>
                            <p style="margin: 6px 0 0; font-size: 14px; color: #d1fae5; font-weight: 500;">
                                Direct Farm Fresh Staples &amp; Premium Gifts
                            </p>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 36px 36px 28px;">
                            
                            <!-- Greeting -->
                            <h2 style="margin: 0 0 14px; font-size: 20px; font-weight: 700; color: #0f172a; line-height: 1.3;">
                                Hello {{ $contact->name }},
                            </h2>
                            <p style="margin: 0 0 20px; font-size: 15px; color: #334155; line-height: 1.6;">
                                Thank you for getting in touch with us! We have received your inquiry regarding <strong>{{ $contact->subject }}</strong>.
                            </p>
                            <p style="margin: 0 0 28px; font-size: 14px; color: #64748b; line-height: 1.6;">
                                Our support team is currently reviewing your message and will get back to you shortly (typically within 12 to 24 business hours).
                            </p>

                            <!-- Copy of Submitted Details -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 28px;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <div style="font-size: 12px; font-weight: 700; text-transform: uppercase; color: #0c716a; letter-spacing: 0.5px; margin-bottom: 12px;">
                                            📋 Summary of Your Submission
                                        </div>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tr>
                                                <td width="30%" style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #64748b;">
                                                    Reference ID:
                                                </td>
                                                <td width="70%" style="padding: 6px 0; font-size: 14px; font-weight: 700; color: #0f172a;">
                                                    #{{ $contact->id }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #64748b; border-top: 1px dashed #e2e8f0;">
                                                    Topic / Subject:
                                                </td>
                                                <td style="padding: 6px 0; font-size: 14px; font-weight: 600; color: #0c716a; border-top: 1px dashed #e2e8f0;">
                                                    {{ $contact->subject }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #64748b; border-top: 1px dashed #e2e8f0;">
                                                    Phone Provided:
                                                </td>
                                                <td style="padding: 6px 0; font-size: 14px; color: #334155; border-top: 1px dashed #e2e8f0;">
                                                    {{ $contact->phone ?? 'N/A' }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 13px; font-weight: 600; color: #64748b; border-top: 1px dashed #e2e8f0;">
                                                    Date Submitted:
                                                </td>
                                                <td style="padding: 6px 0; font-size: 13px; color: #475569; border-top: 1px dashed #e2e8f0;">
                                                    {{ $contact->created_at ? $contact->created_at->format('d M Y, h:i A') : date('d M Y, h:i A') }}
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="margin-top: 14px; padding-top: 12px; border-top: 1px solid #e2e8f0;">
                                            <div style="font-size: 12px; font-weight: 600; color: #64748b; margin-bottom: 6px;">Your Message:</div>
                                            <div style="font-size: 14px; color: #1e293b; line-height: 1.5; font-style: italic; background: #ffffff; padding: 12px; border-radius: 8px; border: 1px solid #e2e8f0;">
                                                "{{ $contact->message }}"
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Urgent Support Callout -->
                            <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 12px; padding: 18px 20px; margin-bottom: 28px;">
                                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                    <tr>
                                        <td>
                                            <div style="font-size: 13px; font-weight: 700; color: #166534; margin-bottom: 4px;">
                                                Need Immediate Assistance?
                                            </div>
                                            <div style="font-size: 13px; color: #15803d; line-height: 1.5;">
                                                Call or WhatsApp our customer care desk at <strong>+91 8778509639</strong> (Mon &ndash; Sat, 8:00 AM &ndash; 8:00 PM IST).
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- CTA Button -->
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="http://127.0.0.1:8000/products" 
                                           style="display: inline-block; padding: 14px 32px; background-color: #0c716a; color: #ffffff; font-size: 14px; font-weight: 700; text-decoration: none; border-radius: 9999px; box-shadow: 0 4px 12px rgba(12, 113, 106, 0.2);">
                                            🛍️ Explore Fresh Products &amp; Gifts
                                        </a>
                                    </td>
                                </tr>
                            </table>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 26px 36px; border-top: 1px solid #e2e8f0; text-align: center;">
                            <p style="margin: 0 0 6px; font-size: 13px; font-weight: 700; color: #334155;">
                                Pal Varnan E-Commerce
                            </p>
                            <p style="margin: 0 0 10px; font-size: 12px; color: #64748b;">
                                Pure, Cold-Pressed Staples &bull; Traditional Goodness &bull; Global Shipping
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                &copy; {{ date('Y') }} Pal Varnan. All rights reserved.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
