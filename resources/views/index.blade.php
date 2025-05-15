<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Todo List | Laravel 10</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-50 min-h-screen">
    <!-- Navbar -->
    <nav class="bg-white shadow-md">
        <div class="container mx-auto px-6 py-3">
            <div class="flex justify-between items-center">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <a href="#" class="text-xl font-bold text-purple-600 flex items-center">
                        <i class="fas fa-check-circle mr-2"></i>
                        TodoApp
                    </a>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    <div class="relative" id="userMenuContainer">
                        <button id="userMenuButton"
                            class="flex items-center text-gray-600 hover:text-purple-600 focus:outline-none">
                            <span
                                class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 mr-2">
                                <i class="fas fa-user"></i>
                            </span>
                            <span>{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </button>
                        <div id="userMenu"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 hidden">
                            <hr class="my-1">
                            <form method="POST" action="{{ route('logout') }}" class="block">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                    ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobileMenuButton" class="text-gray-500 hover:text-purple-600 focus:outline-none">
                        <i id="mobileMenuIcon" class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="md:hidden mt-3 pb-3 hidden">
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50">หน้าหลัก</a>
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50">รายงาน</a>
                <hr class="my-2">
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50">ข้อมูลส่วนตัว</a>
                <a href="#"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-purple-600 hover:bg-purple-50">ตั้งค่า</a>
                <form method="POST" action="{{ route('logout') }}" class="block">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-red-600 hover:bg-red-50">
                        ออกจากระบบ
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8" id="todoAppContainer">
        <!-- Header -->
        <header class="mb-8">
            <div class="mb-4">
                <h2 class="text-xl text-gray-700">ผู้ใช้:
                    <span class="font-semibold text-purple-600">
                        {{ Auth::user()->name }}
                    </span>
                </h2>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl font-bold text-purple-600">รายการสิ่งที่ต้องทำ</h1>
                    <p class="text-gray-500">จัดการงานของคุณอย่างมีประสิทธิภาพ</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-500">
                        <span id="completedTasks">0</span>/<span id="totalTasks">0</span> เสร็จสิ้น
                    </span>
                    <div class="w-40 bg-gray-200 rounded-full h-2.5">
                        <div id="progressBar" class="bg-purple-600 h-2.5 rounded-full" style="width: 0%">
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Add New Task Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-bold text-purple-600 mb-4">เพิ่มงานใหม่</h2>
            <form id="addTaskForm" class="space-y-4">
                @csrf
                <div>
                    <label for="title" class="block text-gray-700 mb-2">ชื่องาน <span
                            class="text-red-500">*</span></label>
                    <input type="text" id="title"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="สิ่งที่ต้องทำ..." required>
                </div>

                <div>
                    <label for="description" class="block text-gray-700 mb-2">คำอธิบาย</label>
                    <textarea id="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                </div>

                <div>
                    <label class="block text-gray-700 mb-2">ความสำคัญ</label>
                    <div class="flex gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="low"
                                class="text-green-500 focus:ring-green-500">
                            <span class="ml-2">ต่ำ</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="medium" checked
                                class="text-yellow-500 focus:ring-yellow-500">
                            <span class="ml-2">ปานกลาง</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="priority" value="high"
                                class="text-red-500 focus:ring-red-500">
                            <span class="ml-2">สูง</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="px-6 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                        <i class="fas fa-plus mr-2"></i> เพิ่มงาน
                    </button>
                </div>
            </form>
        </div>

        <!-- Task Filters -->
        <div class="flex flex-wrap gap-2 mb-6">
            <button id="filterAll"
                class="px-4 py-2 text-sm font-medium rounded-full border bg-purple-100 text-purple-700 border-purple-300 transition-colors">
                ทั้งหมด
            </button>
            <button id="filterActive"
                class="px-4 py-2 text-sm font-medium rounded-full border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 transition-colors">
                กำลังทำ
            </button>
            <button id="filterCompleted"
                class="px-4 py-2 text-sm font-medium rounded-full border bg-white text-gray-700 border-gray-300 hover:bg-gray-50 transition-colors">
                เสร็จสิ้น
            </button>
            <div class="flex-grow"></div>
            <button id="sortButton"
                class="flex items-center gap-1 px-4 py-2 text-sm font-medium bg-white text-gray-700 rounded-full border border-gray-300 hover:bg-gray-50 transition-colors">
                <span>เรียงตามวันที่</span>
                <span id="sortAsc" class="hidden">
                    <i class="fas fa-sort-up"></i>
                </span>
                <span id="sortDesc" class="inline">
                    <i class="fas fa-sort-down"></i>
                </span>
            </button>
        </div>

        <!-- Task List -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div id="taskList" class="divide-y divide-gray-200">
                <!-- Tasks will be rendered here by JavaScript -->
            </div>
            <div id="emptyTaskList" class="p-8 text-center text-gray-500 hidden">
                <div class="text-5xl mb-4">📝</div>
                <h3 class="font-medium text-xl mb-1">ไม่พบรายการ</h3>
                <p>ดูเหมือนคุณยังไม่มีรายการในส่วนนี้</p>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <div id="editModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-xl font-bold mb-4">แก้ไขรายการ</h2>
                <form id="editTaskForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="editTaskId">
                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="edit-title">ชื่อรายการ <span
                                class="text-red-500">*</span></label>
                        <input type="text" id="edit-title"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 mb-2" for="edit-description">คำอธิบาย</label>
                        <textarea id="edit-description" rows="3"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent"></textarea>
                    </div>

                    <div class="mb-6">
                        <label class="block text-gray-700 mb-2" for="edit-priority">ความสำคัญ</label>
                        <select id="edit-priority"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                            <option value="low">ต่ำ</option>
                            <option value="medium">ปานกลาง</option>
                            <option value="high">สูง</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" id="cancelEditButton"
                            class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                            ยกเลิก
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                            บันทึก
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Confirmation Modal -->
        <div id="deleteConfirmModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
            <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
                <h2 class="text-xl font-bold mb-2">ยืนยันการลบ</h2>
                <p class="text-gray-600 mb-6">คุณต้องการลบรายการนี้ใช่หรือไม่? การกระทำนี้ไม่สามารถย้อนกลับได้</p>
                <div class="flex justify-end gap-2">
                    <button id="cancelDeleteButton"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        ยกเลิก
                    </button>
                    <button id="confirmDeleteButton"
                        class="px-4 py-2 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                        ลบ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // State variables
            let tasks = [];
            let filter = 'all';
            let sortDirection = 'desc';
            let taskToDeleteId = null;

            // DOM elements
            const userMenuButton = document.getElementById('userMenuButton');
            const userMenu = document.getElementById('userMenu');
            const mobileMenuButton = document.getElementById('mobileMenuButton');
            const mobileMenu = document.getElementById('mobileMenu');
            const mobileMenuIcon = document.getElementById('mobileMenuIcon');
            const addTaskForm = document.getElementById('addTaskForm');
            const taskList = document.getElementById('taskList');
            const emptyTaskList = document.getElementById('emptyTaskList');
            const filterAllButton = document.getElementById('filterAll');
            const filterActiveButton = document.getElementById('filterActive');
            const filterCompletedButton = document.getElementById('filterCompleted');
            const sortButton = document.getElementById('sortButton');
            const sortAsc = document.getElementById('sortAsc');
            const sortDesc = document.getElementById('sortDesc');
            const editModal = document.getElementById('editModal');
            const editTaskForm = document.getElementById('editTaskForm');
            const editTaskId = document.getElementById('editTaskId');
            const editTitle = document.getElementById('edit-title');
            const editDescription = document.getElementById('edit-description');
            const editPriority = document.getElementById('edit-priority');
            const cancelEditButton = document.getElementById('cancelEditButton');
            const deleteConfirmModal = document.getElementById('deleteConfirmModal');
            const cancelDeleteButton = document.getElementById('cancelDeleteButton');
            const confirmDeleteButton = document.getElementById('confirmDeleteButton');
            const completedTasksElement = document.getElementById('completedTasks');
            const totalTasksElement = document.getElementById('totalTasks');
            const progressBar = document.getElementById('progressBar');

            // User menu toggle
            userMenuButton.addEventListener('click', function() {
                userMenu.classList.toggle('hidden');
            });

            // Close user menu when clicking outside
            document.addEventListener('click', function(event) {
                if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                    userMenu.classList.add('hidden');
                }
            });

            // Mobile menu toggle
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
                if (mobileMenu.classList.contains('hidden')) {
                    mobileMenuIcon.classList.remove('fa-times');
                    mobileMenuIcon.classList.add('fa-bars');
                } else {
                    mobileMenuIcon.classList.remove('fa-bars');
                    mobileMenuIcon.classList.add('fa-times');
                }
            });

            // Fetch tasks from server
            function fetchTasks() {
                fetch('/tasks')
                    .then(response => response.json())
                    .then(data => {
                        tasks = data.map(task => ({
                            ...task,
                            created_at: new Date(task.created_at)
                        }));
                        renderTasks();
                        updateTaskCounters();
                    })
                    .catch(error => {
                        console.error('Error fetching tasks:', error);
                        showError('ไม่สามารถโหลดข้อมูลได้');
                    });
            }

            // Add a new task
            addTaskForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const title = document.getElementById('title').value;
                const description = document.getElementById('description').value;
                const priority = document.querySelector('input[name="priority"]:checked').value;

                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                fetch('/tasks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: title,
                            description: description,
                            priority: priority
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        tasks.push({
                            ...data,
                            created_at: new Date(data.created_at)
                        });
                        addTaskForm.reset();
                        document.querySelector('input[value="medium"]').checked = true;
                        renderTasks();
                        updateTaskCounters();
                        showSuccess('เพิ่มรายการสำเร็จ');
                    })
                    .catch(error => {
                        console.error('Error adding task:', error);
                        showError('ไม่สามารถเพิ่มรายการได้');
                    });
            });

            // Update task status (complete/incomplete)
            function updateTaskStatus(taskId, completed) {
                fetch(`/tasks/${taskId}/status`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            completed: completed
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(() => {
                        const task = tasks.find(t => t.id === taskId);
                        if (task) {
                            task.completed = completed;
                        }
                        renderTasks();
                        updateTaskCounters();
                        showSuccess('อัปเดตสถานะสำเร็จ');
                    })
                    .catch(error => {
                        console.error('Error updating task status:', error);
                        showError('ไม่สามารถอัปเดตสถานะได้');
                    });
            }

            // Edit task - show modal with task data
            function editTask(taskId) {
                const task = tasks.find(t => t.id === taskId);
                if (!task) return;

                editTaskId.value = task.id;
                editTitle.value = task.title;
                editDescription.value = task.description || '';
                editPriority.value = task.priority;

                editModal.classList.remove('hidden');
            }

            // Update task - submit edit form
            editTaskForm.addEventListener('submit', function(e) {
                e.preventDefault();

                const taskId = editTaskId.value;
                const title = editTitle.value;
                const description = editDescription.value;
                const priority = editPriority.value;

                fetch(`/tasks/${taskId}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            title: title,
                            description: description,
                            priority: priority
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        const taskIndex = tasks.findIndex(t => t.id === parseInt(taskId));
                        if (taskIndex !== -1) {
                            tasks[taskIndex] = {
                                ...tasks[taskIndex],
                                title: title,
                                description: description,
                                priority: priority
                            };
                        }
                        closeEditModal();
                        renderTasks();
                        showSuccess('อัปเดตรายการสำเร็จ');
                    })
                    .catch(error => {
                        console.error('Error updating task:', error);
                        showError('ไม่สามารถอัปเดตรายการได้');
                    });
            });

            // Close edit modal
            cancelEditButton.addEventListener('click', closeEditModal);

            function closeEditModal() {
                editModal.classList.add('hidden');
            }

            // Close edit modal when clicking outside
            editModal.addEventListener('click', function(e) {
                if (e.target === editModal) {
                    closeEditModal();
                }
            });

            // Delete task - show confirmation
            function showDeleteConfirm(taskId) {
                taskToDeleteId = taskId;
                deleteConfirmModal.classList.remove('hidden');
            }

            // Close delete confirmation
            cancelDeleteButton.addEventListener('click', function() {
                deleteConfirmModal.classList.add('hidden');
                taskToDeleteId = null;
            });

            // Close delete confirmation when clicking outside
            deleteConfirmModal.addEventListener('click', function(e) {
                if (e.target === deleteConfirmModal) {
                    deleteConfirmModal.classList.add('hidden');
                    taskToDeleteId = null;
                }
            });

            // Confirm and delete task
            confirmDeleteButton.addEventListener('click', function() {
                if (!taskToDeleteId) return;

                fetch(`/tasks/${taskToDeleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(() => {
                        tasks = tasks.filter(t => t.id !== taskToDeleteId);
                        deleteConfirmModal.classList.add('hidden');
                        taskToDeleteId = null;
                        renderTasks();
                        updateTaskCounters();
                        showSuccess('ลบรายการสำเร็จ');
                    })
                    .catch(error => {
                        console.error('Error deleting task:', error);
                        showError('ไม่สามารถลบรายการได้');
                    });
            });

            // Filter tasks
            filterAllButton.addEventListener('click', function() {
                setActiveFilter('all');
            });

            filterActiveButton.addEventListener('click', function() {
                setActiveFilter('active');
            });

            filterCompletedButton.addEventListener('click', function() {
                setActiveFilter('completed');
            });

            function setActiveFilter(newFilter) {
                filter = newFilter;

                // Update button styles
                filterAllButton.className = getFilterButtonClass(filter === 'all');
                filterActiveButton.className = getFilterButtonClass(filter === 'active');
                filterCompletedButton.className = getFilterButtonClass(filter === 'completed');

                renderTasks();
            }

            function getFilterButtonClass(isActive) {
                const baseClass = "px-4 py-2 text-sm font-medium rounded-full border transition-colors";
                return isActive ?
                    `${baseClass} bg-purple-100 text-purple-700 border-purple-300` :
                    `${baseClass} bg-white text-gray-700 border-gray-300 hover:bg-gray-50`;
            }

            // Sort tasks
            sortButton.addEventListener('click', function() {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';

                // Update sort icon
                if (sortDirection === 'asc') {
                    sortAsc.classList.remove('hidden');
                    sortDesc.classList.add('hidden');
                } else {
                    sortAsc.classList.add('hidden');
                    sortDesc.classList.remove('hidden');
                }

                renderTasks();
            });

            // Format date
            function formatDate(date) {
                return new Date(date).toLocaleDateString('th-TH', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            // Update task counters and progress bar
            function updateTaskCounters() {
                const completedCount = tasks.filter(task => task.completed).length;
                const totalCount = tasks.length;
                const percentComplete = totalCount > 0 ? Math.round((completedCount / totalCount) * 100) : 0;

                completedTasksElement.textContent = completedCount;
                totalTasksElement.textContent = totalCount;
                progressBar.style.width = `${percentComplete}%`;
            }

            // Render filtered and sorted tasks
            function renderTasks() {
                // Apply filters
                let filteredTasks = [...tasks];

                if (filter === 'active') {
                    filteredTasks = filteredTasks.filter(task => !task.completed);
                } else if (filter === 'completed') {
                    filteredTasks = filteredTasks.filter(task => task.completed);
                }

                // Apply sorting
                filteredTasks.sort((a, b) => {
                    const dateA = new Date(a.created_at);
                    const dateB = new Date(b.created_at);

                    if (sortDirection === 'asc') {
                        return dateA - dateB;
                    } else {
                        return dateB - dateA;
                    }
                });

                // Check if list is empty
                if (filteredTasks.length === 0) {
                    taskList.innerHTML = '';
                    emptyTaskList.classList.remove('hidden');
                } else {
                    emptyTaskList.classList.add('hidden');

                    // Render task items
                    taskList.innerHTML = filteredTasks.map(task => `
                        <div class="p-4 flex items-center gap-4 transition-colors hover:bg-gray-50
                            ${task.priority === 'high' ? 'border-l-4 border-red-500' :
                            task.priority === 'medium' ? 'border-l-4 border-yellow-500' :
                            'border-l-4 border-green-500'}">
                            <!-- Checkbox -->
                            <div>
                                <input type="checkbox" id="task-${task.id}" 
                                    ${task.completed ? 'checked' : ''}
                                    onchange="updateTaskStatus(${task.id}, this.checked)"
                                    class="h-5 w-5 rounded text-purple-600 focus:ring-purple-500">
                            </div>

                            <!-- Task Content -->
                            <div class="flex-grow">
                                <label for="task-${task.id}" class="flex flex-col cursor-pointer">
                                    <span class="font-medium text-gray-900 transition-all
                                        ${task.completed ? 'line-through text-gray-500' : ''}">${task.title}</span>
                                    <span class="text-sm text-gray-500">${formatDate(task.created_at)}</span>
                                    ${task.description ? `<span class="text-sm text-gray-500 mt-1">${task.description}</span>` : ''}
                                </label>
                            </div>

                            <!-- Priority Badge -->
                            <div>
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    ${task.priority === 'high' ? 'bg-red-100 text-red-800' :
                                    task.priority === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-green-100 text-green-800'}">
                                    ${task.priority === 'high' ? 'สูง' : 
                                      task.priority === 'medium' ? 'ปานกลาง' : 'ต่ำ'}
                                </span>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center gap-2">
                                <button onclick="editTask(${task.id})"
                                    class="text-gray-400 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="showDeleteConfirm(${task.id})"
                                    class="text-gray-400 hover:text-red-600 transition-colors">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                    `).join('');
                }
            }

            // Show success message
            function showSuccess(message) {
                const toast = document.createElement('div');
                toast.className =
                    'fixed top-4 right-4 px-6 py-3 bg-green-500 text-white rounded-lg shadow-lg flex items-center animate-fade-in-up';
                toast.innerHTML = `
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${message}</span>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('animate-fade-in-up');
                    toast.classList.add('animate-fade-out');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Show error message
            function showError(message) {
                const toast = document.createElement('div');
                toast.className =
                    'fixed top-4 right-4 px-6 py-3 bg-red-500 text-white rounded-lg shadow-lg flex items-center animate-fade-in-up';
                toast.innerHTML = `
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span>${message}</span>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.remove('animate-fade-in-up');
                    toast.classList.add('animate-fade-out');
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }

            // Make functions available globally for inline event handlers
            window.updateTaskStatus = updateTaskStatus;
            window.editTask = editTask;
            window.showDeleteConfirm = showDeleteConfirm;

            // Initial load
            fetchTasks();
        });
    </script>

    <style>
        .animate-fade-in-up {
            animation: fadeInUp 0.3s ease-out forwards;
        }

        .animate-fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-20px);
            }
        }
    </style>
</body>

</html>
