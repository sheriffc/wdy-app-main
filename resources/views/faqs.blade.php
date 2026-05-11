@extends('layouts.app')

@section('custom_css')
    @include('assets.datatables-css')
@endsection

@section('content')

    <div class="container mt-2 d-flex justify-content-center">
        <div class="card col-md-9">
            <div class="card-body">
                <h3 class="card-title">Frequently Asked Questions</h3>
                <h5 class="mt-4">What is Wi De Ya? </h5>
                <p>
                    Wi De Ya (meaning <i>'We Are Here'</i> in Krio) is an app-based system for recording teacher and pupil attendance and enrolment.
                    It will be introduced in Government and Government-Assisted schools gradually over the next 3+ years
                    to monitor teacher and pupil attendance and help the Government of Sierra Leone (GoSL) continue transforming education.
                    Its introduction is an important step in the Government’s commitment under the 2018 Free Quality School Education (FQSE) Policy
                    and the delivery of the Radical Inclusion Policy.
                </p>
                <p>
                    Wi De Ya is led by the Teaching Service Commission (TSC) in collaboration with the Ministry of Basic
                    and Senior Secondary Education (MBSSE), developed and tested
                    by <a href="https://cgatechnologies.org.uk/">CGA Technologies</a> (CGA) and
                    funded by the Multi Donor Trust Fund (World Bank, GPE, EU, Irish Aid, and FCDO).
                </p>
                <p>
                    Wi De Ya will strengthen teacher management, enhance transparency and accountability in the teaching service,
                    and improve the integrity of the teacher payroll. In addition, data collected using the app will help TSC and
                    MBSSE make informed decisions about education provision and improvements, including allocation of resources to
                    where they are most needed.
                </p>

                <h5 class="mt-3">How does Wi De Ya work? </h5>
                <p>
                    The Wi De Ya app is installed onto a tablet. School leaders use the app to confirm and edit their
                    teacher lists against centrally-held data, and to add profiles on volunteer teachers and individual
                    learners. They then use the app to record daily teacher attendance via fingerprint verification and
                    pupil attendance via class registers.
                </p>
                <p>
                    Data collected is stored on the tablet and synced to a secure online database for TSC and MBSSE
                    analysis when connected to the internet. Public-facing online dashboards (this website) will display
                    limited non-personal data.
                </p>
                <h6>Whose attendance is monitored? </h6>
                <ul>
                    <li>School leaders use the Wi De Ya app to record the daily attendance of: </li>
                    <ul>
                        <li>All teachers on the Government payroll and all volunteer teachers using biometrics (fingerprints)</li>
                        <li>All pupils via the class register</li>
                    </ul>
                </ul>

                <h5>When will Wi De Ya be fully implemented? </h5>
                <p>
                    Wi De Ya will be implemented in stages.
                    A 300 primary school rollout across all districts starts from March 2023, followed by a planned 1,500
                    and 4,000 school rollout (details to be confirmed).
                    A phased approach ensures school leaders, teachers, and other stakeholders are able to provide
                    feedback and help shape how Wi De Ya works.
                </p>
                <h6>Why monitor teacher attendance? </h6>
                <p>
                    The introduction of Free Quality School Education (FQSE) has seen increased demand for schooling.
                    The GoSL and TSC are committed to supporting this through increased recruitment of teachers,
                    the introduction of new terms and conditions and a recent 45 percent pay rise for teachers.
                    However, it is essential to ensure that existing resources are used wisely.
                </p>
                <p>
                    Poor attendance in the teaching service and challenges to the integrity of the payroll are well
                    documented, and the Sierra Leone Education Sector Plan 2018-2020 identifies the need to strengthen
                    the education system by cleaning the payroll and improving teacher allocation through better records
                    and stronger data management.
                    It is imperative that those teachers who work hard are recognised for their efforts and that the TSC
                    is aware where there are teachers who do not.
                </p>
                <p>
                    Sierra Leone will only realise the benefit of additional teachers and better terms and conditions if
                    we address other problems.
                    As such, we need and expect all teachers on the Government payroll to be committed to their work, to
                    arrive on time, be present in the classroom and to teach effectively to ensure our children get the
                    education they deserve.
                </p>

                <h5>Why monitor the attendance of volunteer teachers? </h5>
                <p>
                    Approximately one third of teachers in government and government assisted schools are volunteers.
                    For an effective education system that delivers quality education, we need sufficient qualified payroll teachers.
                    By collecting data on where volunteer teachers are and their attendance, we can better identify teacher needs
                    across the country, and target recruitment and deployment, with a view to supporting volunteer teachers
                    to qualify, and bring them onto the payroll when the fiscal space is available.
                </p>

                <h5>Why monitor the attendance of students? </h5>
                <p>
                    FQSE has been instrumental in helping many thousands more children attend school. Since 2018, school
                    enrolment has increased dramatically. But we know most marginalised and vulnerable children face
                    insurmountable challenges accessing and staying in school.
                </p>
                <p>
                    The MBSSE’s Radical Inclusion Policy commits the GoSL to making sure every child, including and especially
                    the most vulnerable such as girls and children with disabilities, is able to attend and stay in school.
                </p>
                <p>
                    Individual pupil data will help MBSSE identify children at risk of dropping out of school so they can
                    be targeted with services to support them to remain. For example, if Wi De Ya data shows a child has
                    completed primary school but does not enrol for junior secondary school the following year, this could
                    trigger a follow up with the child and their parent(s)/guardian(s) to find out why and direct the
                    child to support services.
                </p>
                <p>
                    In addition, the data will enable MBSSE to verify how many pupils attend each government and
                    government-assisted school so the Ministry of Finance can pay the correct school subsidies.
                </p>

                <h5>How is the data collected stored and used? </h5>
                <p>
                    All data is stored securely on a cloud-based database, and accessed via a permissioned website:

                </p>
                <ul>
                    <li>
                        Sensitive individual teacher data is only accessible to TSC staff with permission.
                        It will be used to support various TSC management, planning and budgeting processes.
                    </li>
                    <li>
                        Individual pupil data is only accessible to MBSSE staff with permission. It will be
                        used to support various MBSSE management, planning and budgeting processes.
                    </li>
                    <li>
                        A summary of teacher and pupil data (with no personal details) is published online to enable all
                        citizens, including parents and school communities, to monitor Wi De Ya progress.
                    </li>
                </ul>
            </div>
        </div>

    </div>

@endsection

