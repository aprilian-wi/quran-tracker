# Quran Memorization Tracker

A **secure, mobile-friendly, role-based PHP application** to track children's progress in memorizing the Quran.

---

## Features

- **3 Roles**: Superadmin, Teacher, Parent
- **Multi-child per parent**
- **Track**: Reached, In-Progress, Memorized
- **Progress history with timestamps**
- **Full Quran structure**: 30 Juz, 114 Surahs (Arabic + English)
- **Class grouping** (Teacher assigns)
- **Admin-only parent creation**
- **Both Teacher & Parent can update progress**
- **Responsive Dashboard** for all roles
- **100% Secure**: PDO, password_hash, role checks

---

## Installation

```bash
1. Upload all files to your server
2. Import `database/quran_tracker.sql`
3. Run: php database/seed_quran.php
4. Login:
   - Email: admin@qurantracker.com
   - Password: Admin123!