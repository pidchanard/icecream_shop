# 🎤 เตรียมสัมภาษณ์ — Scoop Shop

รวมคำถามที่กรรมการน่าจะถามเกี่ยวกับโปรเจกต์นี้ พร้อมแนวตอบและจุดโค้ดที่ชี้ได้
> เคล็ดลับ: เปิดไฟล์ที่เกี่ยวข้องค้างไว้ในแท็บ editor ล่วงหน้า จะได้โชว์ได้ทันที

---

## 🟦 หมวด 1: ภาพรวมโปรเจกต์

**Q: เล่าโปรเจกต์นี้ให้ฟังหน่อย**
> เว็บ e-commerce ร้านไอศกรีม ทำเองทั้งหมดด้วย PHP แบบ full-stack มี 2 ฝั่ง (ลูกค้า/แอดมิน) ที่เชื่อมข้อมูลกันจริง เช่น แอดมินกดจัดส่ง ลูกค้าเห็น Delivered ทันที

**Q: ส่วนไหนยากที่สุด?**
> การออกแบบให้ข้อมูล 2 ฝั่งสอดคล้องกัน และการ debug เช่น bug `LIMIT 1` ที่ทำให้สั่งของหลายชิ้นแล้วบันทึกแค่ชิ้นเดียว

**Q: ใช้เวลาทำนานไหม / ทำคนเดียวหรือ?**
> ทำคนเดียวทั้งหมด ตั้งแต่ออกแบบข้อมูล, backend, จนถึง UI

---

## 🟩 หมวด 2: เทคนิค / PHP

**Q: ทำไมเลือก PHP?**
> เข้าใจ request–response ชัด, เริ่มได้เร็วไม่ต้อง config, ใช้แพร่หลายในงาน e-commerce, หา hosting ง่าย — เข้าใจ core ก่อนแล้วต่อยอด framework ได้

**Q: ทำไมไม่ใช้ framework?**
> ตั้งใจเขียน core เพื่อเข้าใจกลไกจริง ถ้างานใหญ่ขึ้นจะใช้ Laravel (ได้ MVC, ORM, auth, CSRF มาให้)

**Q: จัดโครงสร้างโค้ดยังไง?**
> แยกส่วนที่ใช้ซ้ำเป็น component (header, footer, connect, auth) เพื่อลดโค้ดซ้ำ
> 👉 โฟลเดอร์ `component/`

---

## 🟨 หมวด 3: ฐานข้อมูล

**Q: ใช้ฐานข้อมูลอะไร?**
> โปรเจกต์นี้ใช้ mock DB เก็บใน session เพื่อให้รันง่ายไม่ต้องตั้ง MySQL แต่เขียนด้วย pattern แบบ PDO (`prepare`/`execute`) เลยพร้อมต่อ MySQL จริงได้
> 👉 `component/connect.php`

**Q: ออกแบบตารางยังไง?**
> มี users, sellers, products, cart, wishlist, orders, message — orders เก็บข้อมูลลูกค้า สินค้า ราคา จำนวน สถานะ และ `order_group` สำหรับรวมออเดอร์

**Q: User ID สร้างยังไง?**
> ฟังก์ชันหาเลขล่าสุดแล้ว +1 เติม 0 เป็น 4 หลัก (c0001, c0002)
> 👉 `component/connect.php` → `next_customer_id()`

---

## 🟥 หมวด 4: ความปลอดภัย (Security)

**Q: กัน SQL Injection ยังไง?**
> ใช้ PDO prepared statements ทุกที่ ไม่ต่อ string ค่าจากผู้ใช้เข้า SQL ตรง ๆ
> 👉 ทุก `$conn->prepare(...)->execute([...])` เช่น `admin panel/login.php`

**Q: เก็บรหัสผ่านยังไง?**
> เข้ารหัสด้วย sha1 ก่อนเก็บ (ไม่ได้เก็บ plain text) — แต่รู้ว่าควรเปลี่ยนเป็น `password_hash()` (bcrypt) สำหรับงานจริง
> 👉 `register.php`, `login.php`

**Q: การเข้าถึงหน้า admin ตรวจสอบยังไง?**
> ใช้ session ฝั่ง server + guard ที่เช็คว่า session มีจริงและตรงกับ seller ในฐานข้อมูล ไม่ผ่านก็ `exit` ทันที + regenerate session id ตอน login กัน session fixation
> 👉 `component/admin_auth.php`, `admin panel/login.php`

**Q: ยิง URL เข้าหน้า admin ตรง ๆ ได้ไหม?**
> ไม่ได้ ทุกหน้า admin include `admin_auth.php` ต้นไฟล์ ถ้าไม่มีสิทธิ์จะ exit ก่อน render
> 👉 บรรทัดบนสุดของหน้า admin ใด ๆ

**Q: ฝั่งลูกค้าล่ะ?**
> ใช้ cookie แต่มี guard เช็คว่า user_id ตรงกับ user จริงในระบบ — ถ้าพัฒนาต่อจะย้ายเป็น session เหมือนฝั่ง admin
> 👉 `component/user_auth.php`

---

## 🟪 หมวด 5: ฟีเจอร์ & การตัดสินใจออกแบบ

**Q: Live Search ทำงานยังไง?**
> ยิง AJAX (fetch) ไป endpoint แล้วเอา HTML การ์ดกลับมาแสดงทันทีขณะพิมพ์
> 👉 `js/user_script.js` (event input) + `search_results.php` + `component/search_cards.php`

**Q: ทำไมของที่จ่ายพร้อมกันเป็นออเดอร์เดียว?**
> สร้าง `order_group` ผูกสินค้าที่จ่ายรอบเดียวกัน แล้ว group ตอนแสดงผล
> 👉 `checkout.php` (`$order_group`), `order.php`, `admin panel/admin_order.php` → `rows_in_group()`

**Q: กันสั่งของหมดสต็อกยังไง?**
> กัน 2 ชั้น — server เช็ค stock ก่อนเพิ่ม/สั่ง, UI ปิดปุ่มซื้อ
> 👉 `component/add_cart.php`, `checkout.php`, `css/user_style.css` (`.box.disabled`)

**Q: ระบบชำระเงินทำอะไรได้บ้าง?**
> เลือกได้หลายวิธี, บัตรเครดิตมี preview สด, วิธีสแกนสร้าง QR จากยอดจริง
> 👉 `checkout.php` → `syncMethod()`, `buildQrCodes()` / `pay.php`

**Q: Pagination ทำยังไง?**
> ดึงทั้งหมดมาแล้ว `array_slice` ตัดทีละหน้า อ่านเลขหน้าจาก `?page=`
> 👉 `order.php`, `admin panel/view_product.php`

---

## 🟫 หมวด 6: การแก้ปัญหา (เล่าให้ดูเก่ง)

**Q: เจอ bug อะไรที่จำได้?**
> สั่งของ 5 ชิ้นจากตะกร้าแต่บันทึกแค่ชิ้นเดียว — ไล่ debug เจอว่ามี `LIMIT 1` ค้างใน query ที่เอามาวน loop พอเอาออกก็ครบ

**Q: มีอะไรที่ปรับปรุงจากของเดิมบ้าง?**
> รวมออเดอร์ด้วย order_group, กันสั่งของหมด, ทำ pagination/filter ทุกหน้า, แก้ราคาแสดงให้คูณจำนวนถูกต้อง

---

## ⬛ หมวด 7: คำถามปลายเปิด / ต่อยอด

**Q: ถ้าให้พัฒนาต่อจะทำอะไร?**
> เปลี่ยน sha1 → bcrypt, ย้ายฝั่งลูกค้าเป็น session, เพิ่ม CSRF, ต่อ MySQL จริง, ตัด stock อัตโนมัติตอนสั่ง, แยกเป็น MVC/Laravel

**Q: ได้เรียนรู้อะไรจากโปรเจกต์นี้?**
> เข้าใจการทำเว็บทั้งระบบ, การออกแบบให้ข้อมูลหลายส่วนสอดคล้องกัน, ความสำคัญของ validation ทั้ง client และ server

**Q: ถ้ามีผู้ใช้เยอะขึ้นจะรับมือยังไง?**
> ใช้ DB จริง + index, ทำ pagination (มีแล้ว), cache query ที่ใช้บ่อย, แยก static files/CDN

---

## ⬜ หมวด 8: Authentication & Access Control (ถามบ่อย)

**Q: มีระบบ authentication ไหม?**
> มีครบครับ — สมัคร, ล็อกอิน (แยกลูกค้า/แอดมิน), ล็อกเอาต์ และ guard ควบคุมสิทธิ์
> 👉 `login.php`, `register.php`, `admin panel/login.php`, `component/user_auth.php`, `component/admin_auth.php` (อยู่ใน admin panel)

**Q: ใช้อะไรแยก admin กับ customer?**
> แยกด้วยโครงสร้าง — คนละตาราง (`users` / `sellers`), คนละที่เก็บตัวตน (cookie / session), คนละ guard ที่ตรวจกับตารางของตัวเอง เลยข้ามสิทธิ์กันไม่ได้

**Q: ทำไมไม่ใช้ role column?**
> เพราะเป็นคนละ entity ชัดเจน แต่ถ้าผู้ใช้ประเภทเดียวมีหลายระดับสิทธิ์ ผมจะใช้ role column หรือ RBAC (Role-Based Access Control) แทน

**Q: ระบบใช้ cookie ไหม?**
> ใช้ครับ ฝั่งลูกค้าเก็บ `user_id` ใน cookie (30 วัน) ตอน login, logout ก็เคลียร์ด้วยการตั้ง expiry เป็นอดีต — ส่วนแอดมินใช้ session (ปลอดภัยกว่า)
> 👉 `login.php`, `component/user_logout.php`

**Q: cookie แบบนี้ปลอดภัยไหม?**
> ยังไม่พอครับ ผมเช็คซ้ำกับ DB แล้ว แต่ถ้าพัฒนาต่อจะย้ายฝั่งลูกค้าเป็น session, ใช้ token ที่เซ็นชื่อ, และเพิ่ม flag `HttpOnly`/`Secure` กัน XSS

**Q: จัดโครงสร้าง auth ยังไง?**
> ทำเป็น guard รวมศูนย์ไฟล์เดียวต่อฝั่ง ทุกหน้าแค่ `include` ต้นไฟล์ — ตามหลัก DRY ไม่เขียนเช็คสิทธิ์ซ้ำทุกหน้า

---

## ⬜ หมวด 9: Real-time / Frontend

**Q: ทำให้สถานะอัปเดตแบบ real-time ยังไง?**
> ใช้ AJAX polling — ฝั่ง user ยิงขอสถานะทุก 4 วินาทีแล้วอัปเดต DOM เอง ไม่ต้องรีเฟรช
> 👉 `order_status.php` (endpoint JSON) + JS ใน `order.php`

**Q: มีวิธีที่ดีกว่า polling ไหม?**
> มีครับ — WebSocket หรือ Server-Sent Events (SSE) ให้ server push มาแทนการ poll จะลด request และเร็วกว่า

**Q: ทำไม PHP?**
> เข้าใจ request–response ชัด, เริ่มเร็วไม่ต้อง config, เหมาะกับงานเว็บ/e-commerce, หา hosting ง่าย — เข้าใจ core ก่อนแล้วต่อยอด framework ได้

---

## ✅ กฎการตอบ 3 ข้อ
1. ตอบ **"ทำไม" ควบคู่ "อะไร"** เสมอ
2. **จุดอ่อนพูดเองก่อน** แล้วบอกวิธีแก้ (โดยเฉพาะ security)
3. **ไม่รู้ ตอบตรง ๆ** ว่า "ยังไม่ได้ทำ แต่รู้ว่าควร..." ดีกว่าเดา
