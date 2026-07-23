from pathlib import Path
from reportlab.lib import colors
from reportlab.lib.enums import TA_CENTER, TA_LEFT
from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Image, PageBreak, Table,
    TableStyle, KeepTogether, HRFlowable
)
from PIL import Image as PILImage


ROOT = Path(r"C:\xampp\htdocs\urbania-backend-api")
OUTPUT = ROOT / "output" / "pdf" / "urbania_external_ticket_event_user_guide.pdf"
SCREENSHOTS = Path(r"C:\Users\MOHAME~1\AppData\Local\Temp")

IMAGES = {
    "ticket_form": SCREENSHOTS / "codex-clipboard-f6c42a00-8146-45bb-87e3-76855ee0161e.png",
    "ticket_delegate": SCREENSHOTS / "codex-clipboard-4b493af4-d93d-44f2-b1de-3bdb9c13aa3f.png",
    "ticket_accepted_email": SCREENSHOTS / "codex-clipboard-e12f41d2-49a0-4360-8520-b120c7402b31.png",
    "ticket_follow_up": SCREENSHOTS / "codex-clipboard-6b8501ae-9c25-4150-9442-23bac5fbe689.png",
    "ticket_staff": SCREENSHOTS / "codex-clipboard-23060671-db32-4762-a694-0d518963a14b.png",
    "ticket_created": SCREENSHOTS / "codex-clipboard-5deeeb89-fac3-4ae0-9c0a-423c191a79a8.png",
    "event_municipality": SCREENSHOTS / "codex-clipboard-63841c1f-b6c7-46a2-b17e-a62feacae4ed.png",
    "event_date": SCREENSHOTS / "codex-clipboard-1c28c5d8-51b2-4e1f-a9ae-10ed037f525b.png",
    "event_time": SCREENSHOTS / "codex-clipboard-8133723d-2fa8-4818-b5c3-6420eb7252d4.png",
    "event_contact": SCREENSHOTS / "codex-clipboard-2a465710-8766-4a21-a920-0805c6be54ba.png",
    "event_delegate": SCREENSHOTS / "codex-clipboard-31a30762-c250-4ca7-abb9-e4e9cbfd47e5.png",
    "event_staff": SCREENSHOTS / "codex-clipboard-182b9b18-7337-4c80-8ec5-53839c6d43a7.png",
    "event_status": SCREENSHOTS / "codex-clipboard-8a68704e-f917-49ef-901a-dfe6366c4cd8.png",
    "event_refused": SCREENSHOTS / "codex-clipboard-2c7275cb-44a8-4a54-b9b8-067b29f60fa3.png",
    "event_confirmed": SCREENSHOTS / "codex-clipboard-6b65982f-82c8-45c0-9de8-c50ab49768de.png",
    "event_control": SCREENSHOTS / "codex-clipboard-320408f5-e759-451f-9fb7-a15923c50b7e.png",
}

RED = colors.HexColor("#E53935")
BLUE = colors.HexColor("#2878E6")
NAVY = colors.HexColor("#10223E")
INK = colors.HexColor("#17243A")
MUTED = colors.HexColor("#526277")
PALE_BLUE = colors.HexColor("#EEF5FF")
PALE_RED = colors.HexColor("#FFF1F0")
LINE = colors.HexColor("#D8E0EA")

styles = getSampleStyleSheet()
styles.add(ParagraphStyle(
    name="CoverTitle", parent=styles["Title"], fontName="Helvetica-Bold",
    fontSize=30, leading=36, textColor=NAVY, alignment=TA_LEFT, spaceAfter=12,
))
styles.add(ParagraphStyle(
    name="CoverSub", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=14, leading=21, textColor=MUTED, spaceAfter=20,
))
styles.add(ParagraphStyle(
    name="H1Guide", parent=styles["Heading1"], fontName="Helvetica-Bold",
    fontSize=21, leading=25, textColor=NAVY, spaceBefore=3, spaceAfter=8,
))
styles.add(ParagraphStyle(
    name="H2Guide", parent=styles["Heading2"], fontName="Helvetica-Bold",
    fontSize=13, leading=17, textColor=NAVY, spaceBefore=6, spaceAfter=5,
))
styles.add(ParagraphStyle(
    name="BodyGuide", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=9.7, leading=14, textColor=INK, spaceAfter=6,
))
styles.add(ParagraphStyle(
    name="Small", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=8.4, leading=11.5, textColor=MUTED,
))
styles.add(ParagraphStyle(
    name="Step", parent=styles["BodyText"], fontName="Helvetica-Bold",
    fontSize=10, leading=14, textColor=BLUE, spaceAfter=2,
))
styles.add(ParagraphStyle(
    name="CardTitle", parent=styles["BodyText"], fontName="Helvetica-Bold",
    fontSize=13, leading=17, textColor=NAVY, spaceAfter=4,
))
styles.add(ParagraphStyle(
    name="CardText", parent=styles["BodyText"], fontName="Helvetica",
    fontSize=9.3, leading=13, textColor=INK,
))


def para(text, style="BodyGuide"):
    return Paragraph(text, styles[style])


def screenshot(key, max_width=16.6*cm, max_height=12.4*cm, border=True):
    path = IMAGES[key]
    if not path.exists():
        raise FileNotFoundError(path)
    with PILImage.open(path) as im:
        width, height = im.size
    scale = min(max_width / width, max_height / height)
    img = Image(str(path), width=width * scale, height=height * scale)
    if border:
        return Table([[img]], colWidths=[width * scale + 8], rowHeights=[height * scale + 8],
                     style=TableStyle([
                        ("BACKGROUND", (0, 0), (-1, -1), colors.white),
                        ("BOX", (0, 0), (-1, -1), 0.5, LINE),
                        ("LEFTPADDING", (0, 0), (-1, -1), 4),
                        ("RIGHTPADDING", (0, 0), (-1, -1), 4),
                        ("TOPPADDING", (0, 0), (-1, -1), 4),
                        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
                     ]))
    return img


def callout(title, text, tint=PALE_BLUE):
    content = [para(title, "Step"), para(text, "Small")]
    return Table([[content]], colWidths=[16.8*cm], style=TableStyle([
        ("BACKGROUND", (0, 0), (-1, -1), tint),
        ("BOX", (0, 0), (-1, -1), 0.5, LINE),
        ("LEFTPADDING", (0, 0), (-1, -1), 9),
        ("RIGHTPADDING", (0, 0), (-1, -1), 9),
        ("TOPPADDING", (0, 0), (-1, -1), 7),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 6),
    ]))


def card(title, text, url, tint=colors.white):
    return Table([[ [para(title, "CardTitle"), para(text, "CardText"), Spacer(1, 7), para(url, "Small")] ]],
        colWidths=[8.0*cm], style=TableStyle([
            ("BACKGROUND", (0, 0), (-1, -1), tint),
            ("BOX", (0, 0), (-1, -1), 0.6, LINE),
            ("LEFTPADDING", (0, 0), (-1, -1), 13),
            ("RIGHTPADDING", (0, 0), (-1, -1), 13),
            ("TOPPADDING", (0, 0), (-1, -1), 13),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 13),
        ]))


def header_footer(canvas, doc):
    canvas.saveState()
    canvas.setStrokeColor(LINE)
    canvas.setLineWidth(0.5)
    canvas.line(doc.leftMargin, A4[1] - 1.35*cm, A4[0] - doc.rightMargin, A4[1] - 1.35*cm)
    canvas.setFillColor(RED)
    canvas.rect(doc.leftMargin, A4[1] - 1.10*cm, 0.42*cm, 0.14*cm, stroke=0, fill=1)
    canvas.setFillColor(NAVY)
    canvas.setFont("Helvetica-Bold", 8.5)
    canvas.drawString(doc.leftMargin + 0.58*cm, A4[1] - 1.19*cm, "URBANIA | External services guide")
    canvas.setFillColor(MUTED)
    canvas.setFont("Helvetica", 8)
    canvas.drawRightString(A4[0] - doc.rightMargin, 0.78*cm, f"Page {doc.page}")
    canvas.drawString(doc.leftMargin, 0.78*cm, "External Ticket and Event modules")
    canvas.restoreState()


def cover(canvas, doc):
    canvas.saveState()
    canvas.setFillColor(RED)
    canvas.rect(0, A4[1] - 2.45*cm, A4[0], 2.45*cm, stroke=0, fill=1)
    canvas.setFillColor(colors.white)
    canvas.setFont("Helvetica-Bold", 11)
    canvas.drawString(doc.leftMargin, A4[1] - 1.45*cm, "URBANIA")
    canvas.setFillColor(colors.HexColor("#F4F7FB"))
    canvas.circle(A4[0] - 2.5*cm, 3.0*cm, 2.4*cm, stroke=0, fill=1)
    canvas.restoreState()


def page_title(story, title, subtitle=None):
    story.append(para(title, "H1Guide"))
    if subtitle:
        story.append(para(subtitle, "BodyGuide"))
    story.append(HRFlowable(width="100%", thickness=0.6, color=LINE, spaceAfter=10))


def step_page(story, title, subtitle, step_label, instruction, image_key, tip=None, max_height=12.4*cm):
    page_title(story, title, subtitle)
    story.append(para(step_label, "Step"))
    story.append(para(instruction))
    if tip:
        story.append(Spacer(1, 3))
        story.append(callout("Good to know", tip))
        story.append(Spacer(1, 9))
    else:
        story.append(Spacer(1, 5))
    story.append(screenshot(image_key, max_height=max_height))
    story.append(PageBreak())


story = []

# Cover
story += [Spacer(1, 2.7*cm), para("How to use Urbania's public services", "CoverTitle"),
          para("A visual guide for submitting an external ticket and requesting an appointment through the Event module.", "CoverSub"),
          HRFlowable(width="42%", thickness=2, color=RED, spaceAfter=22)]
cover_data = [[
    para("<b>For citizens and external users</b><br/>Follow the public links, complete the required fields marked with a red asterisk, and retain the confirmation message or email.", "CardText"),
    para("<b>What this guide covers</b><br/>1. External Ticket: send a service request.<br/>2. External Event: select and request an appointment.<br/>3. What happens after submission.", "CardText")
]]
story.append(Table(cover_data, colWidths=[8.0*cm, 8.0*cm], style=TableStyle([
    ("BACKGROUND", (0,0), (0,0), PALE_BLUE), ("BACKGROUND", (1,0), (1,0), PALE_RED),
    ("BOX", (0,0), (-1,-1), .5, LINE), ("INNERGRID", (0,0), (-1,-1), .5, LINE),
    ("LEFTPADDING", (0,0), (-1,-1), 13), ("RIGHTPADDING", (0,0), (-1,-1), 13),
    ("TOPPADDING", (0,0), (-1,-1), 14), ("BOTTOMPADDING", (0,0), (-1,-1), 14),
])))
story += [Spacer(1, 1.3*cm), para("Portal links", "H2Guide"),
          para("External Ticket: https://urbania.testingelmo.com/external-ticket", "BodyGuide"),
          para("External Event: https://urbania.testingelmo.com/external-event", "BodyGuide"),
          Spacer(1, 1.0*cm), para("This guide reflects the screens supplied for the Urbania portal. Field labels appear in Italian in the application; this guide explains their purpose in English.", "Small"), PageBreak()]

# Overview
page_title(story, "Choose the service you need", "Both modules are public: users do not need to navigate the internal Urbania menu.")
story.append(Table([[card("1. External Ticket", "Use this when you need to report or request a service. You choose the municipality, service and year, then provide your contact details and a clear reason for the request.", "https://urbania.testingelmo.com/external-ticket", PALE_BLUE),
                     card("2. External Event", "Use this to request an appointment. You select the municipality, date and, where available, the time, then provide the attendee's details.", "https://urbania.testingelmo.com/external-event", PALE_RED)]],
    colWidths=[8.25*cm, 8.25*cm], style=TableStyle([("VALIGN", (0,0), (-1,-1), "TOP"), ("LEFTPADDING", (0,0), (-1,-1), 0), ("RIGHTPADDING", (0,0), (-1,-1), 7)])))
story += [Spacer(1, 14), callout("External Ticket workflow", "Client submits the external request -> staff select <b>Accettato</b> -> the acceptance email is sent -> client opens the email link and submits any message/files -> the ticket is created and appears in the <b>Tickets</b> page."),
          Spacer(1, 12), para("Before you begin", "H2Guide"),
          para("Have your personal details ready: first and last name, tax code (<b>C.F.</b>), telephone number and email. If you are acting for someone else, also have the delegate's details and any authorization document required by the portal."),
          callout("Required fields", "A red asterisk (*) identifies information the portal requires before it can accept your request."), PageBreak()]

# Tickets
step_page(story, "External Ticket", "Create a new service request", "STEP 1 - Complete the ticket form",
          "Open the External Ticket link. Select <b>Comune</b> (municipality), <b>Servizio</b> (service) and <b>Anno</b> (year). Enter your personal and contact details, choose the request type, then describe the reason in <b>Motivazione isfcmaz</b>. Select <b>Salva Ticket</b> when every required field is complete.",
          "ticket_form", "Use the description field to state the relevant facts, request and useful references. This helps staff handle the ticket without asking for clarification.", 11.5*cm)

step_page(story, "External Ticket", "Submit a request on behalf of another person", "OPTIONAL - Use the Delegato checkbox",
          "If a delegate is submitting the request, tick <b>Delegato</b>. The form shows the delegate section. Select the delegate's <b>Ruolo</b> (role), enter their first name, last name and telephone number, and use <b>Scarica l'autorizzazione</b> to download the authorization document when needed.",
          "ticket_delegate", "The main personal details remain those of the person concerned by the ticket. The delegate section identifies the person acting for them.", 12.3*cm)

step_page(story, "External Ticket", "Staff approval happens before the email", "STEP 1 - Staff select Accettato",
          "Urbania staff receive the external request in the External Tickets area. They review the customer, service and request details, assign it where appropriate, and select <b>Accettato</b> (Accepted). This is the first action after the client submits the external form. The system sends the acceptance email immediately after this staff approval.",
          "ticket_staff", "Order of this flow: staff select Accettato first; the acceptance email is sent next. The external submission creates a request for staff review; it does not yet create the final ticket in the Tickets page.", 12.4*cm)

step_page(story, "External Ticket", "Acceptance email after staff approval", "STEP 2 - Open the email sent after status is Accettato",
          "After staff select <b>Accettato</b>, the system sends the acceptance email. The email contains a private link. The client must open this link to reach the follow-up area and provide any final message or supporting file before the ticket is created in the Tickets page.",
          "ticket_accepted_email", "The link is personal to the request. Keep it private and do not forward it to anyone who should not access the request.", 11.4*cm)

step_page(story, "External Ticket", "Send a message or attach a file", "STEP 3 - Use the link from the acceptance email",
          "The link opens the <b>Sollecito</b> (follow-up) page. Enter a message for Urbania staff, then use <b>Upload</b> to select and attach a file when supporting documentation is needed. Tick <b>Sollecito</b> if applicable and select <b>Salva</b> (Save). This is the client action that completes the accepted external request.",
          "ticket_follow_up", "Use this page only after staff have accepted the external request. Include files that help staff process the request, and make the message clear and specific.", 10.8*cm)

step_page(story, "External Ticket", "Ticket created in the Tickets page", "STEP 4 - Ticket becomes available in Urbania",
          "After the client saves the message or attachment from the email link, the system creates the ticket. It then appears in the <b>Tickets</b> page, where Urbania staff can manage it through the normal ticket workflow. The ticket number is available for future communication and tracking.",
          "ticket_created", "The flow is complete only when the ticket appears in the Tickets page: staff accept the external request, the client completes the email-link follow-up, and the system creates the ticket.", 10.9*cm)

# Events
step_page(story, "External Event", "Request an appointment", "STEP 1 - Choose the municipality",
          "Open the External Event link. In the first screen, select the required <b>Comune</b> (municipality) from the list, then select <b>Avanti</b> (Next) to continue to the calendar.",
          "event_municipality", "The municipality determines the dates and time slots made available for the appointment.", 10.7*cm)

page_title(story, "External Event", "Select an available date and time")
story.append(para("STEP 2 - Use the calendar", "Step"))
story.append(para("Use the left and right arrows to browse months. Select an available day, then choose a time from the list when times are displayed. Click <b>Salva</b> (Save) to proceed. Use <b>Indietro</b> (Back) if you need to return to the previous step."))
story.append(Spacer(1, 7))
left = screenshot("event_date", max_width=8.05*cm, max_height=7.5*cm)
right = screenshot("event_time", max_width=8.05*cm, max_height=7.5*cm)
story.append(Table([[left, right]], colWidths=[8.25*cm, 8.25*cm], style=TableStyle([
    ("VALIGN", (0,0), (-1,-1), "TOP"), ("LEFTPADDING", (0,0), (-1,-1), 0), ("RIGHTPADDING", (0,0), (-1,-1), 8),
])))
story += [Spacer(1, 11), callout("Availability", "Only dates and times offered by the calendar can be selected. If the preferred slot is unavailable, choose another available slot or return later to check the calendar."), PageBreak()]

step_page(story, "External Event", "Enter the attendee's information", "STEP 3 - Complete and confirm the request",
          "Enter the attendee's <b>Nome</b> (first name), <b>Cognome</b> (last name), <b>C.F.</b> (tax code), telephone number and email. Select <b>Azienda?</b> if relevant, choose a <b>Motivo</b> (reason) when available and add a clear message. Click <b>Conferma</b> (Confirm) to send the reservation request.",
          "event_contact", "Check the email address carefully. Confirmation or update messages are sent to the address entered here.", 12.6*cm)

step_page(story, "External Event", "Book for another person", "OPTIONAL - Add delegate information",
          "Tick <b>Delegato</b> if someone is acting on the attendee's behalf. The form expands to include the delegate area. Enter the delegate's name and surname and use the <b>Scarica</b> (Download) link for the authorization document where required before confirming the request.",
          "event_delegate", "The reservation must still include the details of the person who will attend the appointment.", 12.6*cm)

step_page(story, "What happens after an event is requested", "Reservation handling by Urbania staff", "STAFF FOLLOW-UP",
          "Staff see incoming reservations in <b>Prenotazioni</b> (Reservations), where they can review the attendee, email, telephone number, requested date and status. They process the request and, when the appointment is approved, set its <b>Stato</b> to <b>Accettato</b> (Accepted).", 
          "event_staff", "The client should wait for the staff decision. Submitting the reservation request does not itself make the appointment final.", 12.4*cm)

step_page(story, "Reservation status", "How staff record the outcome", "STAFF FOLLOW-UP",
          "When staff open a reservation, they can set the <b>Stato</b> (status), including options such as <i>Non assegnato</i> (not assigned), <i>Sospeso</i> (suspended) and <i>Accettato</i> (accepted), add a reason or message, and save the update. When <b>Accettato</b> is selected, the system sends the client an email with a confirmation link.",
          "event_status", "The link in the email asks the client to take the final action. The appointment is not final until the client confirms it.", 12.1*cm)

page_title(story, "External Event", "Email sent after staff acceptance")
story.append(para("STEP 4 - Open the email after status is Accettato", "Step"))
story.append(para("After staff select <b>Accettato</b>, the system emails the client with the appointment date and a reservation-control link. The email asks the client to use that link to confirm the reservation or cancel it. Keep the reservation number and date for reference."))
story.append(Spacer(1, 6))
confirm = screenshot("event_confirmed", max_width=15.5*cm, max_height=7.0*cm)
refusal = screenshot("event_refused", max_width=14.0*cm, max_height=4.4*cm)
story.append(Table([[confirm], [Spacer(1, 8)], [refusal]], colWidths=[16.5*cm], style=TableStyle([
    ("LEFTPADDING", (0,0), (-1,-1), 0), ("RIGHTPADDING", (0,0), (-1,-1), 0), ("TOPPADDING", (0,0), (-1,-1), 0), ("BOTTOMPADDING", (0,0), (-1,-1), 0),
])))
story += [Spacer(1, 6), callout("This email needs a response", "Use the link in the acceptance email to confirm or cancel. The email is sent after staff accept the reservation; it is not a final booking until the client completes the response."), PageBreak()]

step_page(story, "External Event", "Confirm or cancel from the email link", "STEP 5 - Make the final reservation decision",
          "Open the private reservation-control link from the email. Select <b>Conferma</b> (Confirm) to accept the proposed appointment, or <b>Rifiuta</b> (Refuse) to cancel it. This client action records the final outcome of the reservation request.",
          "event_control", "Choose Confirm only when the date and time work for the attendee. Select Refuse if the appointment is no longer needed or the slot is unsuitable.", 10.8*cm)

# Close
page_title(story, "Quick checklist", "Use this before submitting either public form")
checklist = [
    [para("<b>1</b>", "CardTitle"), para("Select the correct municipality and service or appointment date.", "CardText")],
    [para("<b>2</b>", "CardTitle"), para("Complete every field marked with a red asterisk (*).", "CardText")],
    [para("<b>3</b>", "CardTitle"), para("Verify your email address and telephone number before submitting.", "CardText")],
    [para("<b>4</b>", "CardTitle"), para("If using a delegate, complete the delegate details and download the authorization document when requested.", "CardText")],
    [para("<b>5</b>", "CardTitle"), para("Keep the ticket number once the ticket is created, plus the reservation number and confirmation emails for your records.", "CardText")],
]
story.append(Table(checklist, colWidths=[1.1*cm, 15.5*cm], style=TableStyle([
    ("BACKGROUND", (0,0), (-1,-1), colors.white), ("BOX", (0,0), (-1,-1), .5, LINE), ("INNERGRID", (0,0), (-1,-1), .4, LINE),
    ("VALIGN", (0,0), (-1,-1), "MIDDLE"), ("ALIGN", (0,0), (0,-1), "CENTER"),
    ("LEFTPADDING", (0,0), (-1,-1), 9), ("RIGHTPADDING", (0,0), (-1,-1), 9),
    ("TOPPADDING", (0,0), (-1,-1), 9), ("BOTTOMPADDING", (0,0), (-1,-1), 9),
])))
story += [Spacer(1, 20), callout("Need to make a correction or follow up?", "For tickets, use the private link in the acceptance email after staff set the status to Accettato, then save the message or attachment so the ticket can be created. For appointments, use the email link sent after staff acceptance to confirm or cancel. If no email arrives, first check the spam/junk folder."),
          Spacer(1, 25), para("Urbania public services guide", "H2Guide"), para("External Ticket: https://urbania.testingelmo.com/external-ticket<br/>External Event: https://urbania.testingelmo.com/external-event", "BodyGuide")]

doc = SimpleDocTemplate(
    str(OUTPUT), pagesize=A4, rightMargin=2.0*cm, leftMargin=2.0*cm,
    topMargin=2.05*cm, bottomMargin=1.65*cm, title="Urbania External Ticket and Event User Guide",
    author="Urbania",
)
doc.build(story, onFirstPage=cover, onLaterPages=header_footer)
print(OUTPUT)
