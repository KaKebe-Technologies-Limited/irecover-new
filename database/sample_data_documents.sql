-- iRecovery Sample Data — 2 documents per category
-- Uses hosted placeholder images (placehold.co) so no local files are
-- needed and images render responsively on mobile out of the box.
-- Safe to run on both local and production — only INSERTs, no deletes.

INSERT INTO `documents`
  (doc_type, sur_name, given_name, dob, gender, id_number, front_img, back_img, action, reporter, reporter_phone, station_holding, payment_status, submitted_at)
VALUES
-- National ID
('national_id', 'NAKATO', 'ESTHER', '1994-03-12', 'female', 'CM94037112AXTZ',
  'https://placehold.co/640x400/0d9488/ffffff?text=National+ID+Front', 'https://placehold.co/640x400/0f766e/ffffff?text=National+ID+Back',
  'found', 'Voice of Lango FM', '0777676206', 'Voice of Lango FM', 'pending', NOW()),
('national_id', 'MUGISHA', 'PATRICK', '1990-11-05', 'male', 'CM90113105BQWK',
  'https://placehold.co/640x400/0d9488/ffffff?text=National+ID+Front', 'https://placehold.co/640x400/0f766e/ffffff?text=National+ID+Back',
  'found', 'Qfm', '0777676206', 'Qfm', 'pending', NOW()),

-- Driving Permit
('driving_permit', 'ATIM', 'GLORIA', '1992-07-21', 'female', 'DP-2024-001122',
  'https://placehold.co/640x400/2563eb/ffffff?text=Driving+Permit+Front', 'https://placehold.co/640x400/1d4ed8/ffffff?text=Driving+Permit+Back',
  'found', 'Voice of The Gospel', '0777676206', 'Voice of The Gospel', 'pending', NOW()),
('driving_permit', 'OKELLO', 'BRIAN', '1988-02-14', 'male', 'DP-2023-998877',
  'https://placehold.co/640x400/2563eb/ffffff?text=Driving+Permit+Front', 'https://placehold.co/640x400/1d4ed8/ffffff?text=Driving+Permit+Back',
  'found', 'Voice of Lango FM', '0777676206', 'Voice of Lango FM', 'pending', NOW()),

-- Passport
('passport', 'NAMUTEBI', 'SARAH', '1996-09-30', 'female', 'B1234567',
  'https://placehold.co/640x400/7c3aed/ffffff?text=Passport+Front', 'https://placehold.co/640x400/6d28d9/ffffff?text=Passport+Back',
  'found', 'Qfm', '0777676206', 'Qfm', 'pending', NOW()),
('passport', 'SSEMPALA', 'JOHN', '1985-01-18', 'male', 'B7654321',
  'https://placehold.co/640x400/7c3aed/ffffff?text=Passport+Front', 'https://placehold.co/640x400/6d28d9/ffffff?text=Passport+Back',
  'found', 'Lira Central Police', '0772100100', 'Lira Central Police', 'pending', NOW()),

-- Student ID
('student_id', 'AKELLO', 'PRECIOUS', '2002-05-09', 'female', 'STU/2024/0456',
  'https://placehold.co/640x400/ea580c/ffffff?text=Student+ID+Front', 'https://placehold.co/640x400/c2410c/ffffff?text=Student+ID+Back',
  'found', 'Voice of Lango FM', '0777676206', 'Voice of Lango FM', 'pending', NOW()),
('student_id', 'WABWIRE', 'DENIS', '2001-12-03', 'male', 'STU/2023/1122',
  'https://placehold.co/640x400/ea580c/ffffff?text=Student+ID+Front', 'https://placehold.co/640x400/c2410c/ffffff?text=Student+ID+Back',
  'found', 'Voice of The Gospel', '0777676206', 'Voice of The Gospel', 'pending', NOW()),

-- Academic Document
('academic_document', 'NABWIRE', 'FLORENCE', '1993-04-27', 'female', 'CERT-UG-8890',
  'https://placehold.co/640x400/0891b2/ffffff?text=Academic+Doc+Front', 'https://placehold.co/640x400/0e7490/ffffff?text=Academic+Doc+Back',
  'found', 'Qfm', '0777676206', 'Qfm', 'pending', NOW()),
('academic_document', 'KATO', 'EMMANUEL', '1991-08-16', 'male', 'CERT-UG-4471',
  'https://placehold.co/640x400/0891b2/ffffff?text=Academic+Doc+Front', 'https://placehold.co/640x400/0e7490/ffffff?text=Academic+Doc+Back',
  'found', 'Voice of Lango FM', '0777676206', 'Voice of Lango FM', 'pending', NOW()),

-- Land Title
('land_title', 'BYARUHANGA', 'MOSES', '1980-06-11', 'male', 'LRV-445-KLA',
  'https://placehold.co/640x400/65a30d/ffffff?text=Land+Title+Front', 'https://placehold.co/640x400/4d7c0f/ffffff?text=Land+Title+Back',
  'found', 'Lira Central Police', '0772100100', 'Lira Central Police', 'pending', NOW()),
('land_title', 'NAMARA', 'JOAN', '1983-10-24', 'female', 'LRV-778-MBR',
  'https://placehold.co/640x400/65a30d/ffffff?text=Land+Title+Front', 'https://placehold.co/640x400/4d7c0f/ffffff?text=Land+Title+Back',
  'found', 'Voice of The Gospel', '0777676206', 'Voice of The Gospel', 'pending', NOW()),

-- Birth Certificate
('birth_certificate', 'TUMWEBAZE', 'IVAN', '2010-01-08', 'male', 'BC-2019-33221',
  'https://placehold.co/640x400/db2777/ffffff?text=Birth+Cert+Front', 'https://placehold.co/640x400/be185d/ffffff?text=Birth+Cert+Back',
  'found', 'Qfm', '0777676206', 'Qfm', 'pending', NOW()),
('birth_certificate', 'ACHENG', 'LILIAN', '2012-03-19', 'female', 'BC-2020-44556',
  'https://placehold.co/640x400/db2777/ffffff?text=Birth+Cert+Front', 'https://placehold.co/640x400/be185d/ffffff?text=Birth+Cert+Back',
  'found', 'Voice of Lango FM', '0777676206', 'Voice of Lango FM', 'pending', NOW()),

-- Other
('other', 'OKIROR', 'SAMUEL', '1975-09-02', 'male', 'OTH-0091',
  'https://placehold.co/640x400/475569/ffffff?text=Document+Front', 'https://placehold.co/640x400/334155/ffffff?text=Document+Back',
  'found', 'Lira Central Police', '0772100100', 'Lira Central Police', 'pending', NOW()),
('other', 'NAKAWEESI', 'RUTH', '1998-12-30', 'female', 'OTH-0245',
  'https://placehold.co/640x400/475569/ffffff?text=Document+Front', 'https://placehold.co/640x400/334155/ffffff?text=Document+Back',
  'found', 'Voice of The Gospel', '0777676206', 'Voice of The Gospel', 'pending', NOW());
