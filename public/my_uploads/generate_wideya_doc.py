"""
Generates Wideya System Technical Documentation as a .docx file.
Run: python3 generate_wideya_doc.py
"""

from docx import Document
from docx.shared import Pt, RGBColor, Inches, Cm
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.style import WD_STYLE_TYPE
from docx.oxml.ns import qn
from docx.oxml import OxmlElement
import os
import copy

OUTPUT = os.path.join(os.path.dirname(__file__), "Wideya_System_Technical_Documentation.docx")

# ── helpers ──────────────────────────────────────────────────────────────────

def add_toc(doc):
    """Insert a Table of Contents field that Word updates on open."""
    para = doc.add_paragraph()
    para.alignment = WD_ALIGN_PARAGRAPH.LEFT
    run = para.add_run()
    fldChar = OxmlElement('w:fldChar')
    fldChar.set(qn('w:fldCharType'), 'begin')
    instrText = OxmlElement('w:instrText')
    instrText.set(qn('xml:space'), 'preserve')
    instrText.text = ' TOC \\o "1-3" \\h \\z \\u '
    fldChar2 = OxmlElement('w:fldChar')
    fldChar2.set(qn('w:fldCharType'), 'separate')
    fldChar3 = OxmlElement('w:fldChar')
    fldChar3.set(qn('w:fldCharType'), 'end')
    run._r.append(fldChar)
    run._r.append(instrText)
    run._r.append(fldChar2)
    run._r.append(fldChar3)


def add_h1(doc, text):
    p = doc.add_heading(text, level=1)
    return p


def add_h2(doc, text):
    p = doc.add_heading(text, level=2)
    return p


def add_h3(doc, text):
    p = doc.add_heading(text, level=3)
    return p


def add_body(doc, text):
    p = doc.add_paragraph(text)
    p.style = doc.styles['Normal']
    return p


def add_bullet(doc, text, level=0):
    p = doc.add_paragraph(text, style='List Bullet')
    return p


def add_table(doc, headers, rows, col_widths=None):
    table = doc.add_table(rows=1 + len(rows), cols=len(headers))
    table.style = 'Table Grid'
    # header row
    hdr_cells = table.rows[0].cells
    for i, h in enumerate(headers):
        hdr_cells[i].text = h
        run = hdr_cells[i].paragraphs[0].runs[0]
        run.bold = True
        hdr_cells[i].paragraphs[0].alignment = WD_ALIGN_PARAGRAPH.CENTER
        tc = hdr_cells[i]._tc
        tcPr = tc.get_or_add_tcPr()
        shd = OxmlElement('w:shd')
        shd.set(qn('w:val'), 'clear')
        shd.set(qn('w:color'), 'auto')
        shd.set(qn('w:fill'), '1F4E79')
        tcPr.append(shd)
        run.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
    # data rows
    for r_idx, row_data in enumerate(rows):
        row_cells = table.rows[r_idx + 1].cells
        for c_idx, cell_text in enumerate(row_data):
            row_cells[c_idx].text = str(cell_text)
            if (r_idx % 2) == 1:
                tc = row_cells[c_idx]._tc
                tcPr = tc.get_or_add_tcPr()
                shd = OxmlElement('w:shd')
                shd.set(qn('w:val'), 'clear')
                shd.set(qn('w:color'), 'auto')
                shd.set(qn('w:fill'), 'D6E4F0')
                tcPr.append(shd)
    if col_widths:
        for row in table.rows:
            for i, cell in enumerate(row.cells):
                cell.width = Inches(col_widths[i])
    return table


def page_break(doc):
    doc.add_page_break()

# ── build document ────────────────────────────────────────────────────────────

doc = Document()

# ── page margins ──────────────────────────────────────────────────────────────
section = doc.sections[0]
section.top_margin    = Cm(2.5)
section.bottom_margin = Cm(2.5)
section.left_margin   = Cm(3.0)
section.right_margin  = Cm(2.5)

# ── title page ────────────────────────────────────────────────────────────────
title_para = doc.add_paragraph()
title_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
run = title_para.add_run("WIDEYA SCHOOL MONITORING SYSTEM")
run.bold = True
run.font.size = Pt(26)
run.font.color.rgb = RGBColor(0x1F, 0x4E, 0x79)

doc.add_paragraph()
sub_para = doc.add_paragraph()
sub_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
run2 = sub_para.add_run("Technical Documentation")
run2.font.size = Pt(18)
run2.font.color.rgb = RGBColor(0x2E, 0x74, 0xB5)

doc.add_paragraph()
doc.add_paragraph()
org_para = doc.add_paragraph()
org_para.alignment = WD_ALIGN_PARAGRAPH.CENTER
run3 = org_para.add_run("Prepared for: Ministry of Education, Sierra Leone\nSystem: Wideya (Android + Web)\nClassification: Internal Technical Reference")
run3.font.size = Pt(12)

page_break(doc)

# ── table of contents ─────────────────────────────────────────────────────────
add_h1(doc, "Table of Contents")
note = doc.add_paragraph("(Right-click this field and select 'Update Field' in Microsoft Word to generate the table of contents.)")
note.runs[0].italic = True
note.runs[0].font.size = Pt(10)
add_toc(doc)
page_break(doc)

# ─────────────────────────────────────────────────────────────────────────────
# 1. EXECUTIVE SUMMARY
# ─────────────────────────────────────────────────────────────────────────────
add_h1(doc, "1. Executive Summary")
add_body(doc,
    "Wideya is a government-grade education monitoring system built for Sierra Leone's Ministry of "
    "Education. It tracks teacher and learner attendance, school infrastructure, school feeding "
    "programmes, learner performance, disability assessments, and administrative compliance across all "
    "schools in every district."
)
add_body(doc,
    "The system operates across two platforms: a Laravel web dashboard used by district officers, "
    "administrators, and ministry officials to view live analytics and manage data; and an Android "
    "mobile application used by head teachers and data entry officers in the field to collect data "
    "offline and synchronise it with the central server when connectivity is available."
)
add_body(doc,
    "The two platforms share a bidirectional sync protocol that keeps data consistent regardless of "
    "whether a device is online or offline. The system manages four categories of people: teachers "
    "(payroll and non-payroll), learners, guardians, and system users. Around these people it tracks: "
    "enrolment, class assignment, daily attendance with biometric verification, academic performance, "
    "disability needs, and school resource supply chains."
)

# ─────────────────────────────────────────────────────────────────────────────
# 2. LANGUAGES, FRAMEWORKS AND TECHNOLOGIES
# ─────────────────────────────────────────────────────────────────────────────
add_h1(doc, "2. Languages, Frameworks and Technologies")

add_h2(doc, "2.1 Web Backend (Server)")
add_table(doc,
    ["Layer", "Technology"],
    [
        ["Language", "PHP 8"],
        ["Framework", "Laravel 9/10"],
        ["Database", "MySQL"],
        ["Authentication", "Laravel Breeze / Sanctum"],
        ["Data Tables", "jQuery DataTables with DataTable Editor (DTE)"],
        ["Charts", "Chart.js"],
        ["Maps", "Leaflet.js with custom GeoJSON district boundaries"],
        ["Templating", "Laravel Blade"],
        ["ORM / Query Builder", "Laravel Eloquent + raw DB facade"],
        ["API Transport", "JSON over HTTP POST"],
        ["Deployment", "Apache / Nginx on Linux"],
    ],
    col_widths=[2.5, 4.0]
)

doc.add_paragraph()
add_h2(doc, "2.2 Android Mobile Application")
add_table(doc,
    ["Layer", "Technology"],
    [
        ["Language", "Kotlin"],
        ["Architecture", "MVVM (Model-View-ViewModel)"],
        ["UI", "Android Fragments + ViewBinding"],
        ["Local Database", "Room (SQLite) encrypted with SQLCipher"],
        ["Networking", "Retrofit 2 with OkHttp"],
        ["Reactive Data", "Kotlin Coroutines + StateFlow / Flow"],
        ["Navigation", "Android Navigation Component"],
        ["Biometrics", "Android Fingerprint SDK"],
        ["Camera", "Android CameraX"],
        ["Location", "Android FusedLocationProvider"],
        ["Image Handling", "Base64 encoding for sync transport"],
    ],
    col_widths=[2.5, 4.0]
)

# ─────────────────────────────────────────────────────────────────────────────
# 3. SYSTEM ARCHITECTURE
# ─────────────────────────────────────────────────────────────────────────────
page_break(doc)
add_h1(doc, "3. System Architecture Overview")
add_body(doc,
    "The system follows an offline-first architecture. The Android application stores all data locally "
    "in an encrypted SQLite database and synchronises with the central MySQL server when network "
    "connectivity is available. The web dashboard reads directly from MySQL in real time."
)
add_body(doc, "High-level data flow:")
add_bullet(doc, "Field staff collect data on Android devices (attendance, profiles, feeding, performance).")
add_bullet(doc, "Data is written to the local encrypted SQLite database and flagged for upload (sync_flag = 1).")
add_bullet(doc, "During sync, flagged records are uploaded to the Laravel API server and saved to MySQL.")
add_bullet(doc, "Reference data (option lists, teacher payroll, academic years, geographic data) is downloaded from the server to all devices.")
add_bullet(doc, "The web dashboard reads from MySQL and presents analytics, maps, and compliance reports to district officers and ministry officials.")

doc.add_paragraph()
add_h2(doc, "3.1 API Endpoints")
add_table(doc,
    ["Endpoint", "Method", "Purpose"],
    [
        ["/api/mobile/registration", "POST", "Login and token generation for Android devices"],
        ["/api/mobile/sync/checkin", "POST", "Device check-in and identification"],
        ["/api/mobile/sync/token", "POST", "Token validation"],
        ["/api/mobile/sync/download", "POST", "Download data payload (up to 5,000 records per request)"],
        ["/api/mobile/sync/upload", "POST", "Upload modified records from device to server"],
        ["/api/mobile/sync/upload-file", "POST", "Upload photos and fingerprint binary files"],
        ["/api/mobile/sync/upload-db-receipt", "POST", "Confirm successful data receipt"],
        ["/api/mobile/teachers/search", "POST", "Search teacher payroll and non-payroll register"],
        ["/api/mobile/password/reset", "POST", "Request password reset from device"],
        ["/api/mobile/check-app-version", "POST", "Check for available app updates"],
    ],
    col_widths=[2.8, 0.9, 3.8]
)

# ─────────────────────────────────────────────────────────────────────────────
# 4. FUNCTIONALITY BREAKDOWN
# ─────────────────────────────────────────────────────────────────────────────
page_break(doc)
add_h1(doc, "4. Functionality Breakdown")

# ── 4.1 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.1 User Authentication and Access Control")
add_h3(doc, "What It Does")
add_body(doc,
    "The web dashboard uses standard Laravel authentication (email and password) with email verification "
    "and an additional admin-approval step. A newly registered web user cannot access any data until an "
    "administrator approves their account. Role-based scoping then limits which schools and districts "
    "each user can see."
)
add_body(doc,
    "The Android app authenticates against the /api/mobile/registration endpoint. Credentials are "
    "transmitted using XOR obfuscation and the server returns an access token stored in the device's "
    "encrypted SharedPreferences. This token is sent with every subsequent sync request. The local "
    "SQLite database is encrypted with SQLCipher so data cannot be read if the device is lost or stolen."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Education data in Sierra Leone is sensitive — learner identities, disability statuses, and teacher "
    "employment records must not be publicly accessible. The layered authentication (web approval + "
    "device token + encrypted storage) protects this data at multiple levels."
)

doc.add_paragraph()
# ── 4.2 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.2 School Profile and Facilities Management")
add_h3(doc, "What It Does")
add_body(doc,
    "Every school in the system has a profile containing its EMIS identification number, Payroll School "
    "ID, geographic coordinates for map plotting, district, chiefdom, and section, head teacher "
    "assignment, and facilities information recorded as structured OID selections:"
)
add_bullet(doc, "WASH — water, sanitation, and hygiene options available at the school")
add_bullet(doc, "Electricity — grid, solar, generator, or none")
add_bullet(doc, "MNO — mobile network operator coverage at the school location")
add_bullet(doc, "Learning Materials — textbooks, slates, exercise books, etc.")
add_bullet(doc, "School Feeding — whether the school participates in the government feeding programme")
add_body(doc,
    "On the Android app the school profile is displayed in a tabbed interface with six tabs: School "
    "details, Teachers, Classrooms, Learners, Feeding, and Performance."
)
add_h3(doc, "Server-Side Enforcement")
add_body(doc,
    "When the Android app submits facility data, the server enforces a 'None is exclusive' rule. If "
    "'none' is selected alongside other options for WASH, electricity, MNO, or learning materials, the "
    "non-none values are stripped out before saving. This prevents logically inconsistent data such as "
    "recording both 'no water' and 'borehole water' simultaneously."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "School infrastructure data informs resource allocation decisions at the district and ministry level. "
    "A complete, accurate facilities inventory allows planners to prioritise schools without clean water, "
    "electricity, or learning materials."
)

doc.add_paragraph()
# ── 4.3 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.3 Academic Year Management")
add_h3(doc, "What It Does")
add_body(doc,
    "Administrators define academic years with a name, start date, and end date. One academic year is "
    "marked as active at a time and drives all enrolment, attendance, and performance data."
)
add_body(doc, "The following protections are built in:")
add_bullet(doc, "No date overlap — if a new academic year's date range overlaps with any existing year, the system rejects it and displays which year it conflicts with and what the conflicting dates are.")
add_bullet(doc, "Past years are read-only — academic years whose end date has already passed cannot be edited or activated. On the web UI, past years show a red 'Past' badge and the Edit and Activate buttons are hidden.")
add_bullet(doc, "Server-side guards — even if someone bypasses the UI, saveAcademicYear and activateAcademicYear independently validate these rules on the server.")
add_bullet(doc, "One active year — activating a new year automatically deactivates any previously active year.")
add_h3(doc, "Why It Matters")
add_body(doc,
    "The academic year is the central context for all attendance, enrolment, and performance records. "
    "If past years were editable or overlapping years were allowed, it would be impossible to accurately "
    "query attendance or enrolment figures because the date boundaries would be ambiguous or corrupted."
)

doc.add_paragraph()
# ── 4.4 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.4 Class and Classroom (School Group) Management")
add_h3(doc, "What It Does")
add_body(doc,
    "Schools are organised into classes called 'school groups.' Each group has a name (e.g. Class 4A, "
    "JSS 2), an assigned teacher, a list of enrolled learners for the current academic year, and an "
    "education level (primary, JSS, SSS)."
)
add_body(doc,
    "A teacher is assigned to one group as their primary class but may appear in multiple groups via "
    "the timetable. Learners are assigned to one group per academic year. A learner without a group "
    "assignment is considered 'unassigned' and appears in a dedicated unassigned learners list."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Class assignment is required before attendance can be taken for learners. The system uses "
    "class-level grouping to calculate class-size statistics, teacher-to-learner ratios, and to scope "
    "attendance and performance reporting to meaningful educational units."
)

doc.add_paragraph()
# ── 4.5 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.5 Teacher Management")
add_h3(doc, "What It Does")
add_body(doc, "The system distinguishes two types of teachers:")
add_bullet(doc, "Payroll teachers — on the government payroll register, identified by a unique PIN. When a head teacher adds a payroll teacher they search the payroll register by name or PIN. The system filters out teachers who are already registered at the school.")
add_bullet(doc, "Non-payroll teachers — teaching staff not on the government payroll, sourced from the teacher_payroll table entries with no PIN. The same school-level exclusion filter applies — once a non-payroll teacher is registered at a school (matched by NIN via the person table), they are hidden from the add-list until removed.")
add_body(doc, "Each teacher profile records:")
add_bullet(doc, "Full name, NIN, date of birth, sex")
add_bullet(doc, "NASSIT number, TSC licence ID")
add_bullet(doc, "Employment status (payroll / non-payroll / volunteer)")
add_bullet(doc, "Teacher role (class teacher, subject teacher, administrator)")
add_bullet(doc, "Portrait photo")
add_bullet(doc, "Up to 10 fingerprints for biometric attendance verification")
add_bullet(doc, "School timetable (subjects and classes taught)")
add_bullet(doc, "Attendance history with absence reasons")
add_bullet(doc, "Start date and, if applicable, end date and reason for leaving")
add_h3(doc, "Payroll Compliance Reports")
add_body(doc, "The web dashboard surfaces four teacher compliance reports:")
add_table(doc,
    ["Report", "Description"],
    [
        ["Sanctions Eligibility", "Teachers who have accumulated enough unauthorised absences to qualify for disciplinary action"],
        ["Unauthorised Transfers", "Teachers physically present at a school different from their payroll assignment"],
        ["Removal Candidates", "Teachers who have been long-term absent with no satisfactory reason"],
        ["Active Teacher List", "Full list of current teachers with status, contact details, and school assignment"],
    ],
    col_widths=[2.2, 5.3]
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Teacher presence is one of the strongest predictors of learning outcomes. Biometric verification "
    "prevents proxy attendance (someone signing in for an absent colleague). The payroll-linked records "
    "allow the ministry to identify ghost teachers, unauthorised transfers, and teachers requiring "
    "disciplinary review from the same data source."
)

doc.add_paragraph()
# ── 4.6 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.6 Learner Admission and Enrolment Management")
add_h3(doc, "What It Does")
add_body(doc, "Learner management operates across two layers:")
add_bullet(doc, "Admission (school_learner_admission) — records that a learner attends this school and captures entry date, exit date, and exit reason. A learner can have admissions at multiple schools over their education history.")
add_bullet(doc, "Enrolment (school_learner_enrolment) — records the learner in a specific class group for a specific academic year. This is the record that links attendance, performance, and disability data to an academic context.")
add_body(doc, "Each learner profile records:")
add_bullet(doc, "Full name (first, middle, last), NIN, date of birth, sex")
add_bullet(doc, "Unique learner ID (auto-generated with school-scoped sequence)")
add_bullet(doc, "Admission number")
add_bullet(doc, "Strongest language spoken")
add_bullet(doc, "Maternal/pregnancy status for female learners")
add_bullet(doc, "Guardian details (name, relation, NIN, phone, address)")
add_bullet(doc, "Class assignment for the current academic year")
add_bullet(doc, "Previous year's class for continuity tracking")
add_body(doc,
    "Learner search allows finding a learner by name, NIN, learner ID, or phone number across all "
    "schools the user has access to. Duplicate detection flags learners with matching NIN or learner "
    "ID at the same school to prevent double-counting."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Accurate enrolment records are the foundation of all education statistics. Without reliable learner "
    "counts and class assignments, attendance rates, gender ratios, disability prevalence, and "
    "performance averages cannot be meaningfully computed."
)

doc.add_paragraph()
# ── 4.7 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.7 Learner Needs Assessment (Disability Tracking)")
add_h3(doc, "What It Does")
add_body(doc,
    "Each learner has a Needs Assessment section that captures the Washington Group Short Set of "
    "disability indicators — the international standard for school-level disability data collection. "
    "Six domains are assessed:"
)
add_table(doc,
    ["Domain", "Question Prompt"],
    [
        ["Vision", "Do they have difficulty seeing, even with glasses?"],
        ["Hearing", "Do they have difficulty hearing, even with a hearing aid?"],
        ["Mobility", "Do they have difficulty walking or climbing steps?"],
        ["Cognition", "Do they have difficulty remembering or concentrating?"],
        ["Self-Care", "Do they have difficulty washing or dressing themselves?"],
        ["Communication", "Do they have difficulty communicating in their usual language?"],
    ],
    col_widths=[1.5, 6.0]
)
doc.add_paragraph()
add_body(doc, "Each domain is rated on a four-point severity scale:")
add_bullet(doc, "No Difficulty")
add_bullet(doc, "Some Difficulty")
add_bullet(doc, "A Lot of Difficulty")
add_bullet(doc, "Cannot Do At All")
add_body(doc,
    "A seventh field records other common conditions such as sickle cell or albinism."
)
add_h3(doc, "No Difficulty for All Shortcut")
add_body(doc,
    "A checkbox at the top of the Needs Assessment section lets the data entry officer set all six "
    "domains to 'No Difficulty' in a single tap — the most common outcome for learners without any "
    "disability. The checkbox auto-checks itself if all six fields are individually set to 'No "
    "Difficulty', and automatically unchecks if any single field is changed to a different severity level."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Sierra Leone's Ministry of Education is committed to inclusive education. This data identifies "
    "schools with high concentrations of learners with disabilities so targeted support (specialist "
    "teachers, adapted materials, physical accessibility improvements) can be directed where needed. "
    "The Washington Group methodology ensures the data is internationally comparable."
)

doc.add_paragraph()
# ── 4.8 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.8 Attendance Recording")
add_h3(doc, "What It Does")
add_body(doc,
    "Attendance is recorded daily for both teachers and learners. Each attendance record captures:"
)
add_bullet(doc, "Person UUID (teacher or learner)")
add_bullet(doc, "Date and school")
add_bullet(doc, "AM and PM session (teachers are marked twice daily)")
add_bullet(doc, "Status: Present, Absent, or Late")
add_bullet(doc, "Absence reason from the option list (sick, family emergency, unauthorised, etc.)")
add_bullet(doc, "Biometric method used: fingerprint scan, photo capture, or manual entry")
add_bullet(doc, "GPS coordinates of the recording device at the moment of marking")
add_bullet(doc, "Photo evidence (base64 encoded, synced separately as a binary file)")
add_h3(doc, "Biometric Verification")
add_body(doc,
    "Fingerprint attendance uses the device's fingerprint scanner. Up to 10 enrolled fingerprints per "
    "person are stored locally and matched entirely on-device — no internet connection is required for "
    "verification. Photo attendance captures an image of the teacher or learner as proof of presence."
)
add_h3(doc, "Web Dashboard Attendance Analytics")
add_bullet(doc, "School-level daily, weekly, and monthly attendance rates")
add_bullet(doc, "Teacher absenteeism trends with reason breakdown")
add_bullet(doc, "Learner absenteeism with at-risk identification")
add_bullet(doc, "District-level heat maps showing attendance patterns geographically")
add_bullet(doc, "Sanctions eligibility (teachers with too many unauthorised absences)")
add_h3(doc, "Why It Matters")
add_body(doc,
    "Teacher attendance is directly correlated with learning outcomes. Biometric verification prevents "
    "proxy sign-in and ghost teacher fraud. GPS coordinates add a further layer of verifiability. "
    "The data feeds directly into payroll compliance monitoring at the ministry level."
)

doc.add_paragraph()
# ── 4.9 ──────────────────────────────────────────────────────────────────────
add_h2(doc, "4.9 Timetable Management")
add_h3(doc, "What It Does")
add_body(doc,
    "Each teacher can have a weekly timetable recorded showing which subjects they teach, in which "
    "classes, on which days, and at what times. The timetable is stored in teacher_timetable and "
    "displayed on the teacher's profile on both the Android app and web dashboard."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Timetable data helps district officers verify that subjects are being taught, identify teachers "
    "with very light schedules (a possible ghost teacher indicator), and assess whether a school has "
    "adequate subject coverage across all classes."
)

doc.add_paragraph()
# ── 4.10 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.10 School Feeding Programme")
add_h3(doc, "What It Does")
add_body(doc,
    "Schools participating in the government feeding programme receive periodic food deliveries. "
    "The system tracks:"
)
add_bullet(doc, "Supplier name and feeding period (start and end dates)")
add_bullet(doc, "Quantities received in kg: rice, beans, gari (cassava flour), vegetable oil, and salt")
add_bullet(doc, "Whether the school actually received the feeding in this period")
add_bullet(doc, "Monthly stock levels for each commodity")
add_body(doc, "The web dashboard provides two dedicated views:")
add_bullet(doc, "School feeding report — per-school history of deliveries, suppliers, quantities, and periods")
add_bullet(doc, "Secretariat view — district-level overview showing all schools' feeding status in a single table for district feeding coordinators")
add_h3(doc, "Why It Matters")
add_body(doc,
    "The school feeding programme is a major learner retention tool, particularly in rural areas where "
    "hunger is a significant dropout factor. Tracking whether food actually arrived at schools allows "
    "the ministry to identify supply chain failures, under-reporting, or schools claiming food they "
    "did not receive."
)

doc.add_paragraph()
# ── 4.11 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.11 Learner Performance and Assessment")
add_h3(doc, "What It Does")
add_body(doc,
    "Each learner's academic performance is tracked per subject, per term, per assessment. The "
    "assessment structure is:"
)
add_bullet(doc, "Three terms: First Term, Second Term, Third Term")
add_bullet(doc, "Two assessments per term — First Assessment and Second Assessment for terms 1 and 2; First Assessment and Final Exam for term 3")
add_bullet(doc, "One score per subject per assessment")
add_body(doc,
    "The Learner Performance tab (embedded as the sixth tab in the School Profile on Android) lists "
    "all learners with a class filter dropdown. Tapping a learner opens the assessment entry screen "
    "where the user selects a term and assessment type, and a list of subjects for that class level "
    "is shown. Each subject row has an editable score field. Scores already saved are locked and "
    "cannot be re-edited. Average and grade (A/B/C/D/F) are calculated live as scores are entered."
)
add_h3(doc, "Save Marks Workflow")
add_table(doc,
    ["Scenario", "Behaviour"],
    [
        ["All subjects have scores entered", "A confirmation dialog appears: 'Do you confirm that the entered grades are correct?' The user must explicitly confirm before saving."],
        ["Some subjects have no score", "A warning dialog lists which subjects are blank. The user can go back to fill them in, or save with the blanks intentionally."],
    ],
    col_widths=[2.5, 5.0]
)
doc.add_paragraph()
add_h3(doc, "Web Dashboard Performance Analytics")
add_bullet(doc, "Average scores by education level and term")
add_bullet(doc, "Subject-level performance comparison across terms")
add_bullet(doc, "Poor performer identification (learners below passing threshold)")
add_bullet(doc, "District and school-level performance trends over time")
add_h3(doc, "Why It Matters")
add_body(doc,
    "Learner performance data allows the education system to identify schools with consistently low "
    "scores, subjects where learning gaps are widespread, and individual learners at risk of failing. "
    "This informs teacher training priorities, curriculum support, and targeted interventions."
)

doc.add_paragraph()
# ── 4.12 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.12 Bidirectional Data Synchronisation")
add_h3(doc, "What It Does")
add_body(doc,
    "This is the core infrastructure that makes the entire system work offline-first. The sync "
    "mechanism operates in two directions:"
)
add_h3(doc, "Download (Server to Device)")
add_bullet(doc, "The device sends its current table state — for each table it reports the max_synced_at timestamp and max_pk of the last record received.")
add_bullet(doc, "The server queries all records newer than those timestamps and returns up to 5,000 records per request.")
add_bullet(doc, "The device upserts all received records into its local SQLite database and updates its table state for the next cycle.")
add_h3(doc, "Upload (Device to Server)")
add_bullet(doc, "Records modified on the device are flagged with sync_flag = 1.")
add_bullet(doc, "During sync, all flagged records are bundled and POST-ed to the upload endpoint.")
add_bullet(doc, "The server upserts each record, recording synced_at, synced_by, and synced_by_install_id.")
add_bullet(doc, "Successfully uploaded records have their sync_flag cleared.")
add_h3(doc, "Data Categories")
add_table(doc,
    ["Category", "Tables", "Direction"],
    [
        ["Universal (shared across all devices)", "Option lists, teacher payroll, non-payroll teachers, academic years, district offices, geographic boundaries", "Server → Device only"],
        ["Binary (school-scoped)", "School details, teachers, learners, attendance, enrolments, performance, timetables, feeding, photos", "Bidirectional"],
    ],
    col_widths=[2.0, 4.5, 1.5]
)
doc.add_paragraph()
add_h3(doc, "File Sync")
add_body(doc,
    "Photos and fingerprint templates are uploaded separately via a dedicated file endpoint. They are "
    "referenced by UUID in attendance and person records. Each sync operation is logged with record "
    "counts and timestamps, visible to the user in the Sync screen."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Most schools in Sierra Leone have unreliable or absent internet connectivity. The offline-first "
    "architecture means data collection is never blocked by network availability. Teachers can record "
    "attendance for an entire week and sync it all when the device comes into range of a WiFi hotspot "
    "or mobile data signal. The sync protocol ensures nothing is lost and the server always ends up "
    "with a complete, accurate picture."
)

doc.add_paragraph()
# ── 4.13 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.13 Biometric Fingerprint Verification")
add_h3(doc, "What It Does")
add_body(doc,
    "The app supports registering up to 10 fingerprint templates per person (all fingers on both "
    "hands). Fingerprints are stored in the person_fingerprint table locally and synced to the server."
)
add_body(doc,
    "During attendance recording, the system verifies the person's identity by matching a live "
    "fingerprint scan against stored templates entirely on-device — no internet connection required. "
    "A successful match records the method as 'fingerprint' in the attendance record alongside GPS "
    "coordinates and timestamp."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Fingerprint verification eliminates proxy attendance. On-device matching means the verification "
    "works in remote areas with no connectivity. The data about how attendance was verified "
    "(fingerprint, photo, manual) is preserved permanently for audit purposes."
)

doc.add_paragraph()
# ── 4.14 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.14 Geographic Mapping and District Analytics")
add_h3(doc, "What It Does")
add_body(doc,
    "The web dashboard includes interactive Leaflet.js maps that display:"
)
add_bullet(doc, "School locations as pins coloured by attendance rate (green = good, amber = moderate, red = poor)")
add_bullet(doc, "Sierra Leone district boundaries from a custom-simplified GeoJSON file")
add_bullet(doc, "Learner distribution by disability and maternal status plotted by school coordinates")
add_bullet(doc, "District-level filtering — clicking a district zooms in and filters all charts and tables to that district")
add_body(doc, "Charts accompanying the maps include:")
add_bullet(doc, "Teacher attendance bar charts by school")
add_bullet(doc, "Gender ratio distributions")
add_bullet(doc, "Disability prevalence by domain")
add_bullet(doc, "Absenteeism trends over time")
add_h3(doc, "Why It Matters")
add_body(doc,
    "Geographic visualisation makes systemic patterns visible that are impossible to see in tables. "
    "A cluster of red dots in a specific district immediately tells a planner where to focus district-"
    "level supervision and resource deployment."
)

doc.add_paragraph()
# ── 4.15 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.15 Administrative User and Scope Management")
add_h3(doc, "What It Does")
add_body(doc, "The administration module covers five areas:")
add_bullet(doc, "User management — administrators approve, reject, or deactivate web dashboard users. Approved users are assigned to scope groups.")
add_bullet(doc, "Scope groups — define which schools and districts a user can see. Scope is enforced on every query so no user can access data outside their permitted schools.")
add_bullet(doc, "District office management — district education offices (DEBS, DEOs) are maintained and linked to schools for organisational reporting.")
add_bullet(doc, "Mobile password reset administration — field users who forget their Android app passwords submit a reset request, which a web administrator reviews and approves.")
add_bullet(doc, "Learner bulk upload — administrators can import learner records via a validated CSV template, reducing data entry burden for large schools.")
add_h3(doc, "Why It Matters")
add_body(doc,
    "Granular access control is essential for a national system. A teacher in Kenema should not be "
    "able to see attendance records for schools in Freetown. Scope groups enforce this without "
    "requiring individual permissions per school — scopes can be defined once at the district level "
    "and all schools in that district are automatically included."
)

doc.add_paragraph()
# ── 4.16 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.16 Reports and Compliance Monitoring")
add_h3(doc, "Teacher Reports")
add_table(doc,
    ["Report", "Description"],
    [
        ["Sanctions Eligibility", "Teachers who have accumulated enough unauthorised absences to qualify for disciplinary action, with full absence counts and details"],
        ["Unauthorised Transfers", "Teachers physically present at a school different from their payroll assignment"],
        ["Removal Candidates", "Teachers who have been long-term absent with no satisfactory reason"],
        ["Active Teacher List", "Full list of current teachers with employment status, contact details, and school assignment"],
    ],
    col_widths=[2.2, 5.3]
)
doc.add_paragraph()
add_h3(doc, "Learner Reports")
add_table(doc,
    ["Report", "Description"],
    [
        ["At-Risk Learners", "Learners with attendance rates below a threshold or performance scores below the passing mark"],
        ["Learners with Disabilities", "Filterable by domain, severity, school, and district using Washington Group indicators"],
        ["Unassigned Learners", "Learners admitted to a school but not placed in a class — prevents attendance from being taken"],
        ["Duplicate Enrolments", "Learners who appear to be enrolled at more than one school simultaneously"],
    ],
    col_widths=[2.2, 5.3]
)
doc.add_paragraph()
add_h3(doc, "Why It Matters")
add_body(doc,
    "These reports are the action layer of the system. They translate raw data into specific, "
    "actionable lists. A district education officer can open the sanctions eligibility report and "
    "immediately know which teachers need letters issued that week. An unassigned learners report "
    "shows which schools need to complete their class lists before attendance data becomes meaningful."
)

doc.add_paragraph()
# ── 4.17 ─────────────────────────────────────────────────────────────────────
add_h2(doc, "4.17 Profile Completion Tracking")
add_h3(doc, "What It Does")
add_body(doc,
    "The system calculates a profile completion percentage for both teachers and learners at each "
    "school. A complete teacher profile includes: full name, NIN, date of birth, sex, employment "
    "status, contact details, portrait photo, and at least one fingerprint registered. A complete "
    "learner profile includes: full name, NIN, date of birth, sex, guardian information, class "
    "assignment, and disability assessment."
)
add_body(doc,
    "Schools and districts are ranked by completion percentage. Schools with low completion rates "
    "are flagged for follow-up data entry work."
)
add_h3(doc, "Why It Matters")
add_body(doc,
    "Incomplete profiles reduce the quality of every downstream report. A learner without a NIN "
    "cannot be de-duplicated against other schools. A teacher without a fingerprint cannot participate "
    "in biometric attendance. Surfacing completion rates at the school level creates accountability "
    "and helps data quality managers prioritise where field visits are needed."
)

# ─────────────────────────────────────────────────────────────────────────────
# 5. DATABASE SCHEMA OVERVIEW
# ─────────────────────────────────────────────────────────────────────────────
page_break(doc)
add_h1(doc, "5. Database Schema Overview")
add_body(doc,
    "The central MySQL database and the Android SQLite database share the same logical schema. "
    "The following table lists the key entities."
)
add_table(doc,
    ["Table", "Type", "Purpose"],
    [
        ["person", "Bi", "Base individual record for all teachers, learners, and guardians"],
        ["teacher", "Bi", "Teacher-specific fields: PIN, nassit, employment status, school, role"],
        ["teacher_payroll", "Uni", "Government payroll register (source of truth for payroll teachers and non-payroll candidates)"],
        ["learner", "Bi", "Learner-specific fields: disabilities, maternal status, guardian link, language"],
        ["school", "Bi", "School records: EMIS ID, payroll SID, facilities OIDs, coordinates"],
        ["school_group", "Bi", "Class/group organisation with teacher assignment and learner counts"],
        ["school_learner_admission", "Bi", "School entry and exit records per learner"],
        ["school_learner_enrolment", "Bi", "Learner enrolment in a class for a specific academic year"],
        ["person_attendance", "Bi", "Daily attendance with biometric method and GPS location"],
        ["teacher_timetable", "Bi", "Teacher lesson schedule by subject, class, day, and time"],
        ["school_academic_year", "Uni", "Active academic year definition with date boundaries"],
        ["school_feeding", "Bi", "Food supply records per school per feeding period"],
        ["school_feeding_stock", "Bi", "Monthly stock levels for each commodity"],
        ["learner_performance", "Bi", "Assessment scores by term, subject, and assessment number"],
        ["media_photo", "Bi", "Base64-encoded photos (portraits and attendance evidence)"],
        ["person_fingerprint", "Bi", "Biometric fingerprint template data (up to 10 per person)"],
        ["option_list", "Uni", "Master OID definitions (employment status, disability levels, absence reasons, etc.)"],
        ["geo", "Uni", "Geographic boundaries and place names"],
        ["district_office", "Uni", "District education offices and their details"],
    ],
    col_widths=[2.5, 0.6, 4.4]
)
doc.add_paragraph()
add_body(doc, "Type key: Uni = unidirectional (server to device only).  Bi = bidirectional (device and server).")

# ─────────────────────────────────────────────────────────────────────────────
# 6. KEY DESIGN DECISIONS
# ─────────────────────────────────────────────────────────────────────────────
page_break(doc)
add_h1(doc, "6. Key Design Decisions")
add_table(doc,
    ["Decision", "Rationale"],
    [
        ["Offline-first with sync", "Unreliable or absent internet connectivity in Sierra Leone schools"],
        ["SQLCipher database encryption", "Sensitive education data on devices that may be lost or stolen"],
        ["UUIDs as primary keys", "Enables conflict-free merging of records created on multiple offline devices"],
        ["Biometric attendance", "Prevents proxy sign-in and ghost teacher fraud"],
        ["Soft deletes (deleted_at)", "Preserves full audit trail; no data is ever permanently destroyed"],
        ["Scope-based access control", "District officers only see their district; no cross-district data leakage"],
        ["'None is exclusive' enforcement", "Prevents logically contradictory facility data from entering the record"],
        ["Versioned sync protocol (V0/V1/V3)", "Allows gradual migration of field devices across protocol versions without forcing simultaneous updates"],
        ["Washington Group disability indicators", "Internationally recognised methodology for school-level disability tracking, enabling Sierra Leone data to be compared globally"],
        ["Server-side academic year guards", "Prevents past years being edited or activated even if the UI is bypassed"],
        ["Non-payroll teacher NIN exclusion filter", "Mirrors payroll PIN exclusion logic so non-payroll teachers cannot be double-registered at a school"],
        ["Confirmation dialogs before saving grades", "Reduces accidental incomplete or incorrect grade submissions"],
    ],
    col_widths=[3.0, 4.5]
)

# ─────────────────────────────────────────────────────────────────────────────
# 7. DATA FLOW SUMMARY
# ─────────────────────────────────────────────────────────────────────────────
page_break(doc)
add_h1(doc, "7. Data Flow Summary")
add_table(doc,
    ["Stage", "Actor", "Action"],
    [
        ["1. Data Collection", "Head teacher / data officer (Android)", "Records attendance, profiles, feeding, and performance locally on device"],
        ["2. Local Storage", "Android SQLite (SQLCipher)", "All records saved with sync_flag = 1 (pending upload)"],
        ["3. Sync Upload", "Android → Laravel API", "Flagged records POSTed to /mobile/sync/upload; sync_flag cleared on success"],
        ["4. Server Storage", "Laravel + MySQL", "Records upserted with synced_at, synced_by, synced_by_install_id"],
        ["5. Reference Sync", "Laravel API → Android", "Option lists, academic years, payroll register downloaded to device"],
        ["6. Web Reporting", "Laravel + MySQL → Browser", "District officers and ministry staff view live analytics, maps, and reports"],
        ["7. Admin Actions", "Web dashboard → MySQL", "User management, scope groups, academic years, bulk uploads"],
    ],
    col_widths=[1.8, 2.2, 3.5]
)

# ─────────────────────────────────────────────────────────────────────────────
# 8. USER ROLES
# ─────────────────────────────────────────────────────────────────────────────
page_break(doc)
add_h1(doc, "8. User Roles")
add_table(doc,
    ["Role", "Platform", "Permissions"],
    [
        ["Head Teacher / Data Officer", "Android", "Record attendance for teachers and learners; manage school profile, classes, and feeding data; enter performance scores; add/remove teachers and learners"],
        ["District Education Officer", "Web", "View attendance, reports, and analytics for all schools in their scoped district; view compliance reports"],
        ["Ministry Official", "Web", "National-level dashboard with all districts; view all reports and analytics"],
        ["System Administrator", "Web", "Full access: user management, scope groups, academic years, district offices, bulk uploads, mobile password resets"],
    ],
    col_widths=[2.0, 1.2, 4.3]
)

# ── footer / save ─────────────────────────────────────────────────────────────
page_break(doc)
add_h1(doc, "Document Information")
add_table(doc,
    ["Field", "Value"],
    [
        ["Document Title", "Wideya School Monitoring System — Technical Documentation"],
        ["Version", "1.0"],
        ["Classification", "Internal Technical Reference"],
        ["Organisation", "CGA Technologies / Ministry of Education, Sierra Leone"],
        ["Platforms Covered", "Laravel Web Backend + Android Mobile Application"],
        ["Primary Technologies", "PHP 8 / Laravel, Kotlin / Android, MySQL, SQLite"],
    ],
    col_widths=[2.5, 5.0]
)

doc.save(OUTPUT)
print(f"Saved: {OUTPUT}")
