# Pse nuk bën push dhe pse ndryshimet nuk duken

## 1. Git nuk të lejon push

**Shkaku:** Repo është në mes të një **rebase** të papërfunduar dhe degat kanë **diverguar** (ti ke 1 commit, origin ka 6 të tjerë).

### Hapat (bëji me radhë):

**Hapi 1 – Mbyll çdo gjë që përdor Git**  
- Mbyll Cursor/VS Code (ose të paktën panelin Source Control).  
- Mbyll Git GUI, Git Bash, ose çdo dritare që e ka hapur këtë repo.  
- Prit 5–10 sekonda.

**Hapi 2 – Fshi skedarin e kyçur (index.lock)**  

**Mënyra e shpejtë – përdor skedarin `fix-git-lock.bat`:**  
1. **Mbyll plotësisht Cursor** (File → Exit ose mbyll dritaren).  
2. Hap **Explorer** dhe shko te folderi:  
   `C:\Users\hello\OneDrive\Desktop\Restaurant Soham\`  
3. **Dypërdytë** mbi skedarin **`fix-git-lock.bat`** (ose kliko të djathtë → Run as administrator).  
4. Nëse ende thotë "Nuk mund të fshihet", mbyll **OneDrive** nga taskbar (kliko djathtas ikonën → Quit OneDrive), pastaj ekzekuto përsëri `fix-git-lock.bat`.

**Me dorë:**  
- Hap Explorer dhe shko te:  
  `C:\Users\hello\OneDrive\Desktop\Restaurant Soham\.git\`  
- Nëse sheh skedarin **`index.lock`**, fshije (kliko të djathtë → Delete).  
- Nëse nuk të lejon, mbyll Cursor dhe OneDrive, pastaj provo përsëri.

**Hapi 3 – Hap Command Prompt ose PowerShell**  
- Kliko Start, shkruaj `cmd` ose `PowerShell`, hape.  
- Shkruaj (shtyp Enter pas çdo rreshti):

```bash
cd "C:\Users\hello\OneDrive\Desktop\Restaurant Soham"
git rebase --abort
```

**Hapi 4 – Sinkronizo me GitHub dhe bëj push**  
- Në të njëjtin terminal:

```bash
git add .
git status
git commit -m "Faza 2: PHP, CRUD, admin, README"
git pull origin DionBranch
```

- Nëse dalin **konflikte**, hap fajllat e theksuar, zgjidh konfliktet (lër versionin që do), pastaj:

```bash
git add .
git commit -m "Zgjidhja e konflikteve"
```

- Më në fund:

```bash
git push origin DionBranch
```

Nëse kërkon **autentikim**, vendos për DionBranch token-in ose fjalëkalimin e GitHub (ose SSH key nëse e ke konfiguruar).

---

## 2. Ndryshimet nuk po “vren” (nuk duken)

Kjo mund të ndodhë nga disa arsye:

**A) Projekti është në Desktop/OneDrive, por localhost lexon nga `htdocs`**  
- XAMPP servon nga: `C:\xampp\htdocs\`.  
- Nëse ti ndryshon vetëm në `Desktop\Restaurant Soham\`, shfletuesi nuk i sheh ato ndryshime.  
- **Zgjidhje:** Kopjo të gjithë folderin **Restaurant Soham** brenda `C:\xampp\htdocs\` dhe hap:  
  `http://localhost/Restaurant%20Soham/index.php`  
- Ose riemërtoje folderin në `restaurant_soham` (pa hapësirë) dhe hap:  
  `http://localhost/restaurant_soham/index.php`

**B) OneDrive e bën folderin “vetëm lexim” ose nuk sinkronizon**  
- Nëse repo është në OneDrive, ndonjëherë fajllat nuk ruhen menjëherë ose duken si të pa ndryshuara.  
- **Zgjidhje:** Pas çdo ndryshimi, ruaj (Ctrl+S) dhe prit disa sekonda; ose puno nga një kopje e projektit jashtë OneDrive (p.sh. direkt në `C:\xampp\htdocs\Restaurant Soham\`) dhe pastaj kopjo përsëri në Desktop për backup.

**C) Shfletuesi e tregon versionin e vjetër (cache)**  
- **Zgjidhje:** Shtyp **Ctrl+F5** (ose Ctrl+Shift+R) në faqen që po testo në localhost.

**D) Duke hapur `index.html` në vend të `index.php`**  
- Nëse hapësh `http://localhost/.../` pa `index.php`, serveri mund të shfaqë `index.html` (versioni i vjetër).  
- **Zgjidhje:** Hap gjithmonë me emër të plotë:  
  `http://localhost/Restaurant%20Soham/index.php`

---

## Përmbledhje

| Problemi              | Çfarë të bësh |
|----------------------|----------------|
| Push nuk funksionon  | Mbyll Cursor/Git → fshi `.git\index.lock` → `git rebase --abort` → `git add .` → `git commit` → `git pull origin DionBranch` → `git push origin DionBranch` |
| Ndryshimet nuk duken | Projekti të jetë në `htdocs`, hap `index.php` me URL të plotë, bëj Ctrl+F5; sigurohu që po ndryshon fajllat në folderin që localhost e servon |
