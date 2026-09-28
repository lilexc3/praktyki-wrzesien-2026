-- EGZAMIN-ARCHIWUM ZSM3; MySQL 8 / MariaDB 10.4+; UTF-8.
-- Import do NOWEJ bazy. Skrypt nie usuwa istniejących tabel ani danych.
CREATE DATABASE IF NOT EXISTS egzamin_zsm3_archiwum CHARACTER SET utf8mb4 COLLATE utf8mb4_polish_ci;
USE egzamin_zsm3_archiwum;
SET NAMES utf8mb4;

CREATE TABLE areas (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 slug VARCHAR(100) NOT NULL UNIQUE,
 icon VARCHAR(30) NOT NULL,
 color CHAR(7) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE qualifications (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 area_id INT UNSIGNED NOT NULL,
 symbol VARCHAR(7) NOT NULL UNIQUE,
 name VARCHAR(255) NOT NULL,
 description TEXT NOT NULL,
 CONSTRAINT fk_qualification_area FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE exams (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 qualification_id INT UNSIGNED NOT NULL,
 year SMALLINT UNSIGNED NOT NULL,
 session ENUM('styczeń','czerwiec','lipiec') NOT NULL,
 exam_type ENUM('teoretyczny','praktyczny') NOT NULL,
 exam_number VARCHAR(20) NOT NULL,
 title VARCHAR(255) NOT NULL,
 description TEXT NOT NULL,
 pdf_path VARCHAR(500) NULL,
 answers_path VARCHAR(500) NULL,
 source_url VARCHAR(1000) NOT NULL DEFAULT '',
 source_name VARCHAR(150) NOT NULL,
 verified_at DATE NULL,
 is_sample BOOLEAN NOT NULL DEFAULT 0,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_exam_qualification FOREIGN KEY (qualification_id) REFERENCES qualifications(id) ON DELETE RESTRICT,
 CONSTRAINT chk_exam_year CHECK (year BETWEEN 2000 AND 2100),
 CONSTRAINT chk_exam_sample CHECK (is_sample IN (0,1)),
 UNIQUE KEY uq_exam (qualification_id,year,session,exam_type,exam_number),
 INDEX idx_exam_filters (year,session,exam_type),
 INDEX idx_exam_created (created_at)
) ENGINE=InnoDB;

CREATE TABLE exam_files (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 exam_id INT UNSIGNED NOT NULL,
 file_name VARCHAR(200) NOT NULL,
 file_path VARCHAR(500) NOT NULL,
 file_type VARCHAR(10) NOT NULL,
 CONSTRAINT fk_file_exam FOREIGN KEY (exam_id) REFERENCES exams(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE admins (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(60) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO areas (id,name,slug,icon,color) VALUES
(1,'Informatyka','informatyka','monitor','#2167d5'),
(2,'Mechatronika i robotyka','mechatronika','gear','#8552d3'),
(3,'Motoryzacja i elektromobilność','motoryzacja','car','#259365'),
(4,'Lotnictwo','lotnictwo','plane','#e88424'),
(5,'Logistyka i terminale','logistyka','ship','#159bb0');

INSERT INTO qualifications (id,area_id,symbol,name,description) VALUES
(1,1,'INF.02','Administracja i eksploatacja systemów komputerowych, urządzeń peryferyjnych i lokalnych sieci komputerowych','Konfiguracja komputerów, systemów operacyjnych, urządzeń peryferyjnych oraz sieci lokalnych.'),
(2,1,'INF.03','Tworzenie i administrowanie stronami i aplikacjami internetowymi oraz bazami danych','Projektowanie stron, programowanie aplikacji internetowych i zarządzanie bazami danych.'),
(3,1,'INF.04','Projektowanie, programowanie i testowanie aplikacji','Projektowanie aplikacji, tworzenie kodu i sprawdzanie poprawności działania oprogramowania.'),
(4,2,'ELM.03','Montaż, uruchamianie i konserwacja urządzeń i systemów mechatronicznych','Montaż podzespołów mechanicznych, elektrycznych, pneumatycznych i hydraulicznych.'),
(5,2,'ELM.06','Eksploatacja i programowanie urządzeń i systemów mechatronicznych','Programowanie sterowników oraz obsługa i diagnostyka systemów mechatronicznych.'),
(6,2,'ELM.07','Montaż, uruchamianie i obsługa systemów robotyki','Montaż i uruchamianie stanowisk zrobotyzowanych oraz obsługa robotów.'),
(7,2,'ELM.08','Eksploatacja i programowanie systemów robotyki','Programowanie robotów, diagnostyka i konserwacja urządzeń w systemach robotyki.'),
(8,3,'MOT.02','Obsługa, diagnozowanie oraz naprawa mechatronicznych systemów pojazdów samochodowych','Diagnostyka i naprawa elektrycznych oraz elektronicznych układów pojazdów.'),
(9,3,'MOT.04','Diagnozowanie, obsługa i naprawa pojazdów motocyklowych','Przeglądy, diagnostyka i naprawa mechanizmów oraz instalacji motocykli.'),
(10,3,'MOT.05','Obsługa, diagnozowanie oraz naprawa pojazdów samochodowych','Obsługa warsztatowa pojazdów, ocena stanu technicznego i naprawy.'),
(11,3,'MOT.06','Organizacja i prowadzenie procesu obsługi pojazdów samochodowych','Planowanie i nadzorowanie prac serwisowych oraz organizacja pracy warsztatu.'),
(12,3,'MOT.07','Organizacja i prowadzenie procesu obsługi i naprawy pojazdów zeroemisyjnych i niskoemisyjnych','Organizacja obsługi pojazdów elektrycznych, hybrydowych i innych pojazdów o ograniczonej emisji.'),
(13,4,'TLO.01','Wykonywanie obsługi technicznej wyposażenia awionicznego i elektrycznego statków powietrznych','Obsługa, diagnostyka i konserwacja awioniki oraz instalacji elektrycznych.'),
(14,4,'TLO.03','Obsługa techniczna statków powietrznych','Obsługa statków powietrznych. Kwalifikacja wcześniejszej podstawy programowej; zachowana dla archiwalnych materiałów.'),
(15,4,'TLO.05','Obsługa naziemna statków powietrznych w porcie lotniczym','Obsługa naziemna samolotów i organizacja czynności w porcie lotniczym. Nowa kwalifikacja od roku szkolnego 2026/2027.'),
(16,4,'TLO.06','Planowanie i koordynacja operacyjna w porcie lotniczym oraz ochrona lotnictwa cywilnego','Koordynowanie operacji lotniskowych i zadania związane z ochroną lotnictwa. Nowa kwalifikacja od roku szkolnego 2026/2027.'),
(17,4,'TLO.07','Obsługa techniczna statku powietrznego i jego instalacji oraz zespołu napędowego','Obsługa konstrukcji, instalacji i napędu statków powietrznych. Nowa kwalifikacja od roku szkolnego 2026/2027.'),
(18,5,'SPL.02','Obsługa podróżnych w portach i terminalach','Organizacja obsługi pasażerów, informacji i dokumentacji w portach oraz terminalach.'),
(19,5,'SPL.03','Obsługa ładunków w portach i terminalach','Przyjmowanie, przechowywanie, przeładunek i wydawanie ładunków oraz dokumentacja transportowa.');

INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,source_name,verified_at,is_sample) VALUES
(2,2025,'styczeń','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(1,2025,'czerwiec','teoretyczny','DEMO','Przykładowy arkusz — część teoretyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(3,2025,'czerwiec','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(4,2025,'styczeń','teoretyczny','DEMO','Przykładowy arkusz — część teoretyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(5,2025,'czerwiec','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(6,2025,'czerwiec','teoretyczny','DEMO','Przykładowy arkusz — część teoretyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(7,2025,'styczeń','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(8,2025,'czerwiec','teoretyczny','DEMO','Przykładowy arkusz — część teoretyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(9,2025,'czerwiec','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(10,2025,'styczeń','teoretyczny','DEMO','Przykładowy arkusz — część teoretyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(11,2024,'czerwiec','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(13,2024,'czerwiec','teoretyczny','DEMO','Przykładowy arkusz — część teoretyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(14,2024,'styczeń','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(18,2024,'czerwiec','teoretyczny','DEMO','Przykładowy arkusz — część teoretyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1),
(19,2024,'czerwiec','praktyczny','DEMO','Przykładowy arkusz — część praktyczna','Rekord demonstracyjny do nauki obsługi archiwum. Nie zawiera oficjalnego arkusza egzaminacyjnego. Dodaj zweryfikowane pliki w panelu administratora.','Dane demonstracyjne projektu',NULL,1);

INSERT INTO admins (username,password_hash) VALUES
('admin','$2y$10$ZXrphgYxm8cmfhD7N4tN9eYBeTro3xGvS9TZ6/h7uBRSQcjiNXZPq');

-- Rzeczywiste arkusze CKE, pozyskane z archiwum arkusze.pl.
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Ranking gier — aplikacja internetowa','Arkusz praktyczny, sesja czerwiec 2025, zadanie 01. Sprawdzono zgodność kodu arkusza i zasad oceniania z kwalifikacją, rokiem i sesją. Dołączono archiwum ZIP z bazą SQL, obrazami i plikiem rekord.txt.','uploads/INF.03/2025/czerwiec/praktyczny/inf03-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/INF.03/2025/czerwiec/praktyczny/inf03-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-inf-03-2025-czerwiec/','2026-09-25',0 FROM qualifications WHERE symbol='INF.03';
INSERT INTO exam_files (exam_id,file_name,file_path,file_type) VALUES (LAST_INSERT_ID(),'Materiały do zadania — ZIP','uploads/INF.03/2025/czerwiec/praktyczny/inf03-2025-czerwiec-egzamin-zawodowy-praktyczny-zalaczniki.zip','zip');
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Modernizacja napędu i programowanie PLC','Arkusz praktyczny, sesja czerwiec 2025, zadanie 01. Sprawdzono zgodność kodu arkusza i zasad oceniania z kwalifikacją, rokiem i sesją.','uploads/ELM.06/2025/czerwiec/praktyczny/elm06-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/ELM.06/2025/czerwiec/praktyczny/elm06-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-elm-06-2025-czerwiec/','2026-09-25',0 FROM qualifications WHERE symbol='ELM.06';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Diagnostyka i naprawa półosi napędowej','Arkusz praktyczny, sesja czerwiec 2025, zadanie 01. Sprawdzono zgodność kodu arkusza i zasad oceniania z kwalifikacją, rokiem i sesją.','uploads/MOT.05/2025/czerwiec/praktyczny/mot05-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/MOT.05/2025/czerwiec/praktyczny/mot05-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-mot-05-2025-czerwiec/','2026-09-25',0 FROM qualifications WHERE symbol='MOT.05';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Obsługa okresowa silnika Rotax 912','Arkusz praktyczny, sesja czerwiec 2025, zadanie 01. Sprawdzono zgodność kodu arkusza i zasad oceniania z kwalifikacją, rokiem i sesją.','uploads/TLO.03/2025/czerwiec/praktyczny/tlo03-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/TLO.03/2025/czerwiec/praktyczny/tlo03-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-tlo-03-2025-czerwiec/','2026-09-25',0 FROM qualifications WHERE symbol='TLO.03';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Organizacja podróży kolejowej i lotniczej','Arkusz praktyczny, sesja czerwiec 2025, zadanie 01. Sprawdzono zgodność kodu arkusza i zasad oceniania z kwalifikacją, rokiem i sesją.','uploads/SPL.02/2025/czerwiec/praktyczny/spl02-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/SPL.02/2025/czerwiec/praktyczny/spl02-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-spl-02-2025-czerwiec/','2026-09-25',0 FROM qualifications WHERE symbol='SPL.02';

-- Materiały sprawdzone 30.09.2026: kwalifikacja, rok, część praktyczna,
-- numer zadania i komplet zasad oceniania zostały zweryfikowane w plikach PDF.
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/INF.02/2025/czerwiec/praktyczny/inf02-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/INF.02/2025/czerwiec/praktyczny/inf02-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-inf-02-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='INF.02';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/INF.04/2025/czerwiec/praktyczny/inf04-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/INF.04/2025/czerwiec/praktyczny/inf04-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-inf-04-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='INF.04';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/ELM.03/2025/czerwiec/praktyczny/elm03-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/ELM.03/2025/czerwiec/praktyczny/elm03-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-elm-03-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='ELM.03';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/ELM.07/2025/czerwiec/praktyczny/elm07-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/ELM.07/2025/czerwiec/praktyczny/elm07-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-elm-07-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='ELM.07';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/ELM.08/2025/czerwiec/praktyczny/elm08-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/ELM.08/2025/czerwiec/praktyczny/elm08-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-elm-08-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='ELM.08';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/MOT.02/2025/czerwiec/praktyczny/mot02-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/MOT.02/2025/czerwiec/praktyczny/mot02-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-mot-02-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='MOT.02';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/MOT.04/2025/czerwiec/praktyczny/mot04-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/MOT.04/2025/czerwiec/praktyczny/mot04-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-mot-04-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='MOT.04';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/MOT.06/2025/czerwiec/praktyczny/mot06-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/MOT.06/2025/czerwiec/praktyczny/mot06-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-mot-06-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='MOT.06';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/TLO.01/2025/czerwiec/praktyczny/tlo01-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/TLO.01/2025/czerwiec/praktyczny/tlo01-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-tlo-01-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='TLO.01';
INSERT INTO exams (qualification_id,year,session,exam_type,exam_number,title,description,pdf_path,answers_path,source_name,source_url,verified_at,is_sample) SELECT id,2025,'czerwiec','praktyczny','01','Arkusz praktyczny — czerwiec 2025, zadanie 01','Rzeczywisty arkusz CKE z sesji czerwiec 2025. Zweryfikowano symbol kwalifikacji, rok, część praktyczną, numer zadania oraz odpowiadające zasady oceniania.','uploads/SPL.03/2025/czerwiec/praktyczny/spl03-2025-czerwiec-egzamin-zawodowy-praktyczny.pdf','uploads/SPL.03/2025/czerwiec/praktyczny/spl03-2025-czerwiec-egzamin-zawodowy-praktyczny-zasady-oceniania.pdf','CKE / archiwum arkusze.pl','https://arkusze.pl/egzamin-zawodowy-spl-03-2025-czerwiec/','2026-09-30',0 FROM qualifications WHERE symbol='SPL.03';
