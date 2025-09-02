<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Control Dashboard - BitsOfDev Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <i class="fas fa-robot text-2xl text-blue-600 mr-3"></i>
                        <h1 class="text-2xl font-bold text-gray-900">AI Control Dashboard</h1>
                    </div>
                    <div class="text-sm text-gray-500">
                        BitsOfDev Admin Panel
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    {{ session('error') }}
                </div>
            @endif

            <!-- AI Settings Card -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">
                    <i class="fas fa-cog mr-2 text-blue-600"></i>
                    AI System Settings
                </h2>

                <form method="POST" action="{{ route('admin.ai.update-settings') }}" class="space-y-6">
                    @csrf

                    <!-- AI Provider Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                AI Provider
                            </label>
                            <select name="ai_provider"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="gemini" {{ $settings->ai_provider == 'gemini' ? 'selected' : '' }}>
                                    <i class="fas fa-robot mr-2"></i>Google Gemini
                                </option>
                                <option value="own_ai" {{ $settings->ai_provider == 'own_ai' ? 'selected' : '' }}>
                                    <i class="fas fa-brain mr-2"></i>Own AI (Training Mode)
                                </option>
                                <option value="none" {{ $settings->ai_provider == 'none' ? 'selected' : '' }}>
                                    <i class="fas fa-ban mr-2"></i>Disabled
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Learning Threshold
                            </label>
                            <input type="number" name="learning_threshold" value="{{ $settings->learning_threshold }}"
                                min="1" max="100"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Minimum responses needed to activate own AI</p>
                        </div>
                    </div>

                    <!-- Toggle Switches -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Training Mode</h3>
                                <p class="text-xs text-gray-500">Use own AI instead of external AI</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="training_mode" value="1"
                                    {{ $settings->training_mode ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Static Responses</h3>
                                <p class="text-xs text-gray-500">Enable pre-defined answers</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="use_static_responses" value="1"
                                    {{ $settings->use_static_responses ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Save Button -->
                    <div class="flex justify-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-save mr-2"></i>
                            Save Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">
                    <i class="fas fa-bolt mr-2 text-yellow-600"></i>
                    Quick Actions
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Switch to Gemini -->
                    <form method="POST" action="{{ route('admin.ai.switch-provider') }}" class="inline">
                        @csrf
                        <input type="hidden" name="provider" value="gemini">
                        <button type="submit"
                            class="w-full bg-blue-600 text-white px-4 py-3 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <i class="fas fa-robot mr-2"></i>
                            Switch to Gemini
                        </button>
                    </form>

                    <!-- Switch to Own AI -->
                    <form method="POST" action="{{ route('admin.ai.switch-provider') }}" class="inline">
                        @csrf
                        <input type="hidden" name="provider" value="own_ai">
                        <button type="submit"
                            class="w-full bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="fas fa-brain mr-2"></i>
                            Switch to Own AI
                        </button>
                    </form>

                    <!-- Activate Learned Responses -->
                    <form method="POST" action="{{ route('admin.ai.activate-learned') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="w-full bg-purple-600 text-white px-4 py-3 rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <i class="fas fa-play mr-2"></i>
                            Activate Learned ({{ $learningStats['pending_review'] }})
                        </button>
                    </form>
                </div>
            </div>

            <!-- Learning Statistics -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">
                    <i class="fas fa-chart-line mr-2 text-green-600"></i>
                    AI Learning Statistics
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-3xl font-bold text-blue-600">{{ $learningStats['total_learned'] }}</div>
                        <div class="text-sm text-gray-600">Total Learned</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-3xl font-bold text-green-600">{{ $learningStats['active_learned'] }}</div>
                        <div class="text-sm text-gray-600">Active Responses</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 rounded-lg">
                        <div class="text-3xl font-bold text-yellow-600">{{ $learningStats['pending_review'] }}</div>
                        <div class="text-sm text-gray-600">Pending Review</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-3xl font-bold text-purple-600">{{ $learningStats['learning_progress'] }}%
                        </div>
                        <div class="text-sm text-gray-600">Learning Progress</div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                        <span>AI Learning Progress</span>
                        <span>{{ $learningStats['learning_progress'] }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full"
                            style="width: {{ $learningStats['learning_progress'] }}%"></div>
                    </div>
                    @if ($learningStats['can_activate_own_ai'])
                        <div class="mt-2 text-sm text-green-600">
                            <i class="fas fa-check-circle mr-1"></i>
                            Ready to activate own AI!
                        </div>
                    @else
                        <div class="mt-2 text-sm text-gray-500">
                            Need {{ $settings->learning_threshold - $learningStats['active_learned'] }} more responses
                            to activate own AI
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Learned Questions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">
                    <i class="fas fa-history mr-2 text-gray-600"></i>
                    Recent Learned Questions
                </h2>

                @if ($recentQuestions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Question</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Answer Preview</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($recentQuestions as $qa)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ Str::limit($qa->question, 50) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ Str::limit($qa->answer_1, 80) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if ($qa->is_active)
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $qa->created_at->format('M j, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-4"></i>
                        <p>No learned questions yet. Start using the chatbot to build your AI knowledge base!</p>
                    </div>
                @endif
            </div>
        </main>
    </div>

    <script>
        // Auto-refresh page every 30 seconds to show latest data
        setTimeout(function() {
            location.reload();
        }, 30000);
    </script>
</body>

</html>
