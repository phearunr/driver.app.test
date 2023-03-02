<template>
    <div v-if="userPermissions.canAddTeamMembers">
        <SectionBorder />

        <!-- Add Team Member -->
        <FormSection @submitted="addTeamMember">
            <template #title>
                Add Role
            </template>

            <template #description>
                Add a new role to your user, allowing user to collaborate with you.
            </template>

            <template #form>
                <div class="col-span-6">
                    <div class="max-w-xl text-sm text-gray-600">
                        Please provide the email address of the person you would like to add to this team.
                    </div>
                </div>

                <!-- Member Email -->
                <div class="col-span-6 sm:col-span-4">
                    <InputLabel for="email" value="Email" />
                    <TextInput
                        id="email"
                        v-model="addTeamMemberForm.email"
                        type="email"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="addTeamMemberForm.errors.email" class="mt-2" />
                </div>

                <!-- Role -->
                <div v-if="availableRoles.length > 0" class="col-span-6 lg:col-span-4">
                    <InputLabel for="roles" value="Role" />
                    <InputError :message="addTeamMemberForm.errors.role" class="mt-2" />

                    <div class="relative z-0 mt-1 border border-gray-200 rounded-lg cursor-pointer">
                        <button
                            v-for="(role, i) in availableRoles"
                            :key="role.key"
                            type="button"
                            class="relative px-4 py-3 inline-flex w-full rounded-lg focus:z-10 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200"
                            :class="{'border-t border-gray-200 rounded-t-none': i > 0, 'rounded-b-none': i != Object.keys(availableRoles).length - 1}"
                            @click="addTeamMemberForm.role = role.key"
                        >
                            <div :class="{'opacity-50': addTeamMemberForm.role && addTeamMemberForm.role != role.key}">
                                <!-- Role Name -->
                                <div class="flex items-center">
                                    <div class="text-sm text-gray-600" :class="{'font-semibold': addTeamMemberForm.role == role.key}">
                                        {{ role.name }}
                                    </div>

                                    <svg
                                        v-if="addTeamMemberForm.role == role.key"
                                        class="ml-2 h-5 w-5 text-green-400"
                                        fill="none"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    ><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>

                                <!-- Role Description -->
                                <div class="mt-2 text-xs text-gray-600 text-left">
                                    {{ role.description }}
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </template>

            <template #actions>
                <ActionMessage :on="addTeamMemberForm.recentlySuccessful" class="mr-3">
                    Added.
                </ActionMessage>

                <PrimaryButton :class="{ 'opacity-25': addTeamMemberForm.processing }" :disabled="addTeamMemberForm.processing">
                    Add
                </PrimaryButton>
            </template>
        </FormSection>
    </div>
</template>
