import 'package:app/providers/contribution_provider.dart';
import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

class VolContribution extends StatefulWidget {
  final int userId;
  const VolContribution({super.key, required this.userId});

  @override
  State<VolContribution> createState() => _VolContributionState();
}

class _VolContributionState extends State<VolContribution> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final provider = Provider.of<ContributionProvider>(context, listen: false);
      // await provider.getVolContributions(userId: widget.userId);
      // con = provider.contribution;  
    });
  }
  @override
  Widget build(BuildContext context) {
    return const Placeholder();
  }
}